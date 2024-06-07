<?php
declare(strict_types=1);

namespace zin;

class productsBox extends wg
{
    /**
     * Define widget properties.
     *
     * @var    array
     * @access protected
     */
    protected static array $defineProps = array(
        'productItems?: array',         // 产品列表。
        'branchGroups?: array',         // 产品分支分组列表。
        'planGroups?: array',           // 产品计划分组列表。
        'linkedProducts?: array',       // 关联的产品。
        'linkedBranches?: array',       // 关联的分支。
        'currentProduct?: int=0',       // 来源产品ID。
        'currentPlan?: int=0',          // 来源计划。
        'productPlans?: array=array()', // 同来源计划所属产品的计划列表。
        'project?: object',             // 关联的项目。
        'isStage?: bool',               // 是否是阶段类型。
        'hasNewProduct?: bool=false',   // 是否有新产品。
        'errorSameProducts?: string',   // 选择同一个产品的提示。
        'required?: bool=false',        // 是否是必填。
        'from?: string=project',        // 来源类型。
        'selectTip?: string=""'         // 产品下拉提示。
    );

    public static function getPageCSS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): ?string
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function build()
    {
        list($project, $productItems, $linkedProducts, $errorSameProducts) = $this->prop(array('project', 'productItems', 'linkedProducts', 'errorSameProducts'));

        $productsBox = array();
        if((!empty($project->hasProduct) || is_null($project)) && $linkedProducts)
        {
            $productsBox = $this->buildLinkedProducts($linkedProducts);
        }
        elseif(!empty($project) && empty($project->hasProduct) && !in_array($project->model, array('waterfall', 'kanban', 'waterfallplus')))
        {
            $productsBox = $this->buildOnlyLinkPlans($linkedProducts);
        }
        else
        {
            $productsBox = $this->initProductsBox();
        }

        return div
        (
            setClass('productsBox'),
            on::click('.productsBox .addLine', 'window.addNewLine'),
            on::click('.productsBox .removeLine', 'window.removeLine'),
            on::change('.productsBox [name^=products]', 'window.loadBranches'),
            jsVar('multiBranchProducts', data('multiBranchProducts')),
            jsVar('project', \zget($project, 'id', 0)),
            jsVar('errorSameProducts', $errorSameProducts),
            $productsBox
        );
    }

    protected function initProductsBox(): array
    {
        global $lang, $app;
        list($productItems, $project, $isStage, $hasNewProduct) = $this->prop(array('productItems', 'project', 'isStage', 'hasNewProduct'));

        $productsBox   = array();
        $hidden        = !empty($project) && empty($project->hasProduct) ? 'hidden' : '';
        $productsBox[] = $hasNewProduct ? div
        (
            setClass('addProductBox flex hidden'),
            formGroup
            (
                on::change()->call('toggleNewProduct'),
                set::width('1/2'),
                set::checkbox(array('text' => $lang->project->addProduct, 'name' => 'newProduct', 'checked' => false)),
                set::required(true),
                set::label($lang->project->manageProducts),
                set::name('productName')
            )
        ) : null;
        $productsBox[] = div
        (
            set::className("productBox flex items-center $hidden"),
            formGroup
            (
                on::change()->call('toggleNewProduct'),
                set::width('1/2'),
                setClass('linkProduct'),
                set::required($this->prop('required') || ($project && in_array($project->model, array('waterfall', 'waterfallplus')))),
                set::label($lang->project->manageProducts),
                set::labelFor('productBox'),
                $hasNewProduct ? set::checkbox(array('text' => $lang->project->addProduct, 'name' => 'addProduct', 'checked' => false)) : false,
                picker
                (
                    set::name('products[0]'),
                    set::items($productItems),
                    !empty($project) && empty($project->hasProduct) ? set::value(current(array_keys($productItems))) : null,
                    set::placeholder($this->prop('selectTip'))
                ),
            ),
            formGroup
            (
                set::width('1/4'),
                setClass('hidden linkBranch ml-px'),
                set::label(''),
                inputGroup
                (
                    setClass('branchBox'),
                    picker
                    (
                        set::name('branch[0][]'),
                        set::items(array()),
                        set::multiple(true),
                        on::change("branchChange")
                    )
                )
            ),
            formGroup
            (
                set::width('1/2'),
                set::label($lang->project->associatePlan),
                set::className('planBox'),
                inputGroup
                (
                    set::id("plan0"),
                    picker
                    (
                        set::name('plans[0][]'),
                        set::items(array()),
                        set::multiple(true)
                    )
                ),
            ),
            ($isStage && isset($project->stageBy) && $project->stageBy == 'product') ? null : div
            (
                setClass('pl-2 flex self-center line-btn c-actions first-action'),
                btn
                (
                    setClass('btn btn-link text-gray addLine'),
                    icon('plus')
                ),
                btn
                (
                    setClass('btn btn-link text-gray removeLine'),
                    setClass('hidden'),
                    icon('trash')
                )
            )
        );

        return $productsBox;
    }

    protected function buildOnlyLinkPlans(array $productItems): array
    {
        global $lang;
        list($currentPlan, $productPlans, $from) = $this->prop(array('currentPlan', 'productPlans', 'from'));

        $planProductID = current(array_keys($productItems));
        $productsBox   = array();
        $productsBox[] = $from == 'execution' ? div
        (
            set::className('productBox noProductBox'),
            formGroup
            (
                set::width('1/2'),
                set::label($lang->execution->linkPlan),
                set::className('planBox'),
                picker
                (
                    set::name("plans[{$planProductID}][]"),
                    set::items(!empty($productPlans) ? $productPlans : array()),
                    set::value($currentPlan),
                    set::multiple(true),
                    formHidden("products[{$planProductID}]", $planProductID),
                    formHidden("branch[{$planProductID}][0]", 0)
                )
            ),
            formHidden("products[{$planProductID}]", $planProductID),
            formHidden("branch[{$planProductID}][0]", 0)
        ) : div
        (
            set::className('productBox'),
            formHidden("products[{$planProductID}]", $planProductID),
            formHidden("branch[{$planProductID}][0]", 0)
        );

        return $productsBox;
    }

    protected function buildLinkedProducts(array $linkedProducts): array
    {
        if(empty($linkedProducts)) return array();

        global $lang;
        list($productItems, $branchGroups, $planGroups, $productPlans) = $this->prop(array('productItems', 'branchGroups', 'planGroups', 'productPlans'));
        list($linkedBranches, $currentProduct, $currentPlan, $project, $isStage) = $this->prop(array('linkedBranches', 'currentProduct', 'currentPlan', 'project', 'isStage'));

        $unmodifiableProducts = data('unmodifiableProducts') ? data('unmodifiableProducts') : array();

        $linkedProductsBox = array();
        foreach(array_values($linkedProducts) as $i => $product)
        {
            $hasBranch = $product->type != 'normal' && isset($branchGroups[$product->id]);
            $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();

            $disabledProduct = !empty($project) && (in_array($product->id, $unmodifiableProducts) || $isStage);

            $branchIdList = '';
            if(isset($product->branches))             $branchIdList = $product->branches;
            if(!empty($linkedBranches[$product->id])) $branchIdList = is_array($linkedBranches) ? array_keys($linkedBranches[$product->id]) : $linkedBranches[$product->id];

            $planID = 0;
            if(empty($currentProduct) || ($currentProduct != $product->id))
            {
                $plans = array();
                if(is_array($branchIdList) && isset($planGroups[$product->id]))
                {
                    foreach($branchIdList as $branchID)
                    {
                        if(isset($planGroups[$product->id][$branchID])) $plans += $planGroups[$product->id][$branchID];
                    }
                }
                $planID = isset($product->plans) && is_array($product->plans)? implode(',', $product->plans) : '';
            }
            else
            {
                $plans  = !empty($productPlans) ? $productPlans : array();
                $planID = isset($currentPlan) && isset($productPlans[$currentPlan]) ? $currentPlan : '';
            }
            if($planID and empty($plans)) $planID = '';

            $linkedProductsBox[] = div
            (
                set::className('productBox flex'),
                formGroup
                (
                    set::width($hasBranch ? '1/4' : '1/2'),
                    setClass('linkProduct'),
                    set::required($this->prop('required') || ($project && in_array($project->model, array('waterfall', 'waterfallplus')))),
                    $i == 0 ? set::label($lang->project->manageProducts) : null,
                    inputGroup
                    (
                        div
                        (
                            setClass('grow'),
                            picker
                            (
                                set::name("products[$i]"),
                                set::value($product->id),
                                set::items($productItems),
                                set::last($product->id),
                                set::disabled($disabledProduct),
                                $i === 0 ? set::placeholder($this->prop('selectTip')) : null,
                                $hasBranch ? set::lastBranch(empty($product->branches) ? 0 : implode(',', $product->branches)) : null,
                                $disabledProduct ? formHidden("products[$i]", $product->id) : null
                            )
                        )
                    )
                ),
                formGroup
                (
                    set::width('1/4'),
                    setClass('ml-px linkBranch'),
                    count($linkedProductsBox) == 0 ? set::label('') : null,
                    $hasBranch ? null : setClass('hidden'),
                    inputGroup
                    (
                        setClass('branchBox'),
                        picker
                        (
                            set::name("branch[$i][]"),
                            set::items($branches),
                            set::value(is_array($branchIdList) ? implode(',', $branchIdList) : $branchIdList),
                            set::disabled($disabledProduct),
                            set::multiple(true),
                            on::change("branchChange")
                        )
                    )
                ),
                $disabledProduct ? div
                (
                    setClass('hidden branchBoxHidden'),
                    picker
                    (
                        set::name("branch[$i][]"),
                        set::items($branches),
                        set::multiple(true),
                        set::value(is_array($branchIdList) ? implode(',', $branchIdList) : $branchIdList)
                    )
                ) : null,
                formGroup
                (
                    set::width('1/2'),
                    $i == 0 ? set::label($lang->project->associatePlan) : null,
                    set::className('planBox'),
                    inputGroup
                    (
                        set::id("plan{$i}"),
                        picker
                        (
                            set::name("plans[$product->id][]"),
                            set::items($plans),
                            set::value($planID),
                            set::multiple(true)
                        )
                    )
                ),
                $disabledProduct ? null : div
                (
                    setClass('pl-2 flex self-center line-btn c-actions', $i == 0 ? 'first-action' : ''),
                    btn
                    (
                        setClass('btn btn-link text-gray addLine'),
                        icon('plus')
                    ),
                    btn
                    (
                        setClass('btn btn-link text-gray removeLine'),
                        setClass($i == 0 ? 'hidden' : ''),
                        icon('trash')
                    )
                )
            );
        }

        return $linkedProductsBox;
    }
}

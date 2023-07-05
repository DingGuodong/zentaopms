<?php
declare(strict_types=1);
/**
 * The zen file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      sunguangming <sunguangming@easycorp.ltd>
 * @link        https://www.zentao.net
 */
class projectZen extends project
{
    /**
     * Append extras data to post data.
     *
     * @param  object $postData
     * @access protected
     * @return object|false
     */
    protected function prepareCreateExtras(object $postData): object|false
    {
        $project = $postData->setDefault('status', 'wait')
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->delta == 999, 'days', 0)
            ->setIF($this->post->acl   == 'open', 'whitelist', '')
            ->setIF(!isset($this->post->whitelist), 'whitelist', '')
            ->setIF(!isset($this->post->multiple), 'multiple', '1')
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('openedDate', helper::now())
            ->setDefault('team', $this->post->name)
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setDefault('days', '0')
            ->add('type', 'project')
            ->join('whitelist', ',')
            ->join('auth', ',')
            ->stripTags($this->config->project->editor->create['id'], $this->config->allowedTags)
            ->get();

        if(!isset($this->config->setCode) || $this->config->setCode == 0) unset($project->code);

        /* Lean mode relation defaultProgram. */
        if($this->config->systemMode == 'light') $project->parent = $this->config->global->defaultProgram;

        if(!$this->checkProductAndBranch($project, (object)$_POST))  return false;
        if(!$this->checkDaysAndBudget($project, (object)$_POST))     return false;
        if(!$this->checkProductNameUnqiue($project, (object)$_POST)) return false;

        return $project;
    }

    /**
     * Check work days legtimate.
     *
     * @param  object $project
     * @access protected
     * @return bool
     */
    protected function checkWorkdaysLegtimate($project): bool
    {
        $workdays = helper::diffDate($project->end, $project->begin) + 1;
        if(isset($project->days) && $project->days > $workdays)
        {
            dao::$errors['days'] = sprintf($this->lang->project->workdaysExceed, $workdays);
            return false;
        }
        return true;
    }

    /**
     * Validate $postData and prepare $project for update.
     *
     * @param  object       $postData
     * @param  int          $hasProduct
     * @access protected
     * @return object|false
     */
    protected function prepareProject(object $postData, int $hasProduct): object|false
    {
        $project = $postData->setDefault('team', $this->post->name)
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setDefault('days', '0')
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->delta == 999, 'days', 0)
            ->setIF($this->post->future, 'budget', 0)
            ->setIF($this->post->budget != 0, 'budget', round((float)$this->post->budget, 2))
            ->join('whitelist', ',')
            ->join('auth', ',')
            ->stripTags($this->config->project->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if($hasProduct)
        {
            /* Check if products not empty. */
            if(!count(array_filter($this->post->products))) return false;

            $project->parent = (int)$project->parent;
            /* Check if products and branch valid. */
            if(!$this->project->checkBranchAndProduct($project->parent, (array)$this->post->products, (array)$this->post->branch)) return false;
        }

        /* Check if work days legtimate. */
        if(!$this->checkWorkdaysLegtimate($project)) return false;

        if(!isset($this->config->setCode) || $this->config->setCode == 0) unset($project->code);

        /* Lean mode relation defaultProgram. */
        if($this->config->systemMode == 'light') $project->parent = $this->config->global->defaultProgram;

        return $project;
    }

    /**
     * Check product and branch not empty.
     *
     * @param  object $project
     * @param  object $rawdata
     * @access protected
     * @return bool
     */
    private function checkProductAndBranch(object $project, object $rawdata): bool
    {
        $linkedProductsCount = $this->project->getLinkedProductsCount($project, $rawdata);

        if($rawdata->products)
        {
            $topProgramID     = (int)$this->loadModel('program')->getTopByID($project->parent);
            $multipleProducts = $this->loadModel('product')->getMultiBranchPairs($topProgramID);
            foreach($rawdata->products as $index => $productID)
            {
                if(isset($multipleProducts[$productID]) && empty($rawdata->branch[$index]))
                {
                    dao::$errors['branch[0]'] = $this->lang->project->error->emptyBranch;
                    return false;
                }
            }
        }

        /* Judge products not empty. */
        if($project->parent && $project->hasProduct && empty($linkedProductsCount) && !isset($rawdata->newProduct))
        {
            dao::$errors['products[0]'] = $this->lang->project->error->productNotEmpty;
            return false;
        }

        return true;
    }

    /**
     * Check days and budget by rules.
     *
     * @param  object $project
     * @param  object $rawdata
     * @access protected
     * @return bool
     */
    private function checkDaysAndBudget(object $project, object $rawdata): bool
    {
        /* Judge workdays is legitimate. */
        $workdays = helper::diffDate($project->end, $project->begin) + 1;
        if(isset($project->days) && $project->days > $workdays)
        {
            dao::$errors['days'] = sprintf($this->lang->project->workdaysExceed, $workdays);
            return false;
        }

        if(!empty($project->budget))
        {
            if(!is_numeric($project->budget))
            {
                dao::$errors['budget'] = sprintf($this->lang->project->error->budgetNumber);
                return false;
            }
            elseif(is_numeric($project->budget) && ($project->budget < 0))
            {
                dao::$errors['budget'] = sprintf($this->lang->project->error->budgetGe0);
                return false;
            }
            else
            {
                $project->budget = round((float)$rawdata->budget, 2);
            }
        }

        return true;
    }

    /**
     * Check product name unique and not empty.
     *
     * @param  object $project
     * @param  object $rawdata
     * @access protected
     * @return bool
     */
    private function checkProductNameUnqiue(object $project, object $rawdata): bool
    {
        /* When select create new product, product name cannot be empty and duplicate. */
        if($project->hasProduct && isset($rawdata->newProduct))
        {
            if(empty($rawdata->productName))
            {
                $this->app->loadLang('product');
                dao::$errors['productName'] = sprintf($this->lang->error->notempty, $this->lang->product->name);
                return false;
            }
            else
            {
                $programID        = isset($project->parent) ? $project->parent : 0;
                $existProductName = $this->dao->select('name')->from(TABLE_PRODUCT)->where('name')->eq($rawdata->productName)->andWhere('program')->eq($programID)->fetch('name');
                if(!empty($existProductName))
                {
                    dao::$errors['productName'] = $this->lang->project->error->existProductName;
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Send variables to create page.
     *
     * @param  string $model
     * @param  int    $programID
     * @param  int    $copyProjectID
     * @param  string $extra
     * @access protected
     * @return void
     */
    protected function buildCreateForm(string $model, int $programID, int $copyProjectID, string $extra): void
    {
        $this->loadModel('product');
        $this->loadModel('program');

        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        if($this->app->tab == 'program' && $programID)                   $this->loadModel('program')->setMenu($programID);
        if($this->app->tab == 'product' && !empty($output['productID'])) $this->loadModel('product')->setMenu($output['productID']);
        if($this->app->tab == 'doc') unset($this->lang->doc->menu->project['subMenu']);

        if($copyProjectID) $copyProject = $this->getCopyProject((int)$copyProjectID);
        $shadow = empty($copyProject->hasProduct) ? 1 : 0;

        if($this->view->globalDisableProgram) $programID = $this->config->global->defaultProgram;
        $topProgramID = $this->program->getTopByID($programID);

        if($model == 'kanban')
        {
            $this->lang->project->aclList    = $this->lang->project->kanbanAclList;
            $this->lang->project->subAclList = $this->lang->project->kanbanSubAclList;
        }

        $withProgram   = $this->config->systemMode == 'ALM';
        $allProducts   = array('0' => '') + $this->program->getProductPairs($programID, 'all', 'noclosed', '', $shadow, $withProgram);
        $parentProgram = $this->loadModel('program')->getByID($programID);

        $this->view->title               = $this->lang->project->create;
        $this->view->gobackLink          = (isset($output['from']) && $output['from'] == 'global') ? $this->createLink('project', 'browse') : '';
        $this->view->model               = $model;
        $this->view->pmUsers             = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst');
        $this->view->users               = $this->user->getPairs('noclosed|nodeleted');
        $this->view->programID           = $programID;
        $this->view->productID           = isset($output['productID']) ? $output['productID'] : 0;
        $this->view->branchID            = isset($output['branchID']) ? $output['branchID'] : 0;
        $this->view->allProducts         = $allProducts;
        $this->view->multiBranchProducts = $this->product->getMultiBranchPairs((int)$topProgramID);
        $this->view->copyProjects        = $this->project->getPairsByModel($model);
        $this->view->copyPinyinList      = common::convert2Pinyin($this->view->copyProjects);
        $this->view->copyProjectID       = $copyProjectID;
        $this->view->parentProgram       = $parentProgram;
        $this->view->programList         = $this->program->getParentPairs();
        $this->view->URSRPairs           = $this->loadModel('custom')->getURSRPairs();
        $this->view->availableBudget     = $this->program->getBudgetLeft($parentProgram);
        $this->view->budgetUnitList      = $this->project->getBudgetUnitList();

        $this->display();
    }

    /**
     * Send variables to edit page.
     *
     * @param  int       $projectID
     * @param  object    $project
     * @access protected
     * @return void
     */
    protected function buildEditForm(int $projectID, object $project): void
    {
        $productPlans        = array();
        $linkedBranchList    = array();
        $parentProject       = $this->loadModel('program')->getByID($project->parent);
        $projectStories      = $this->project->getStoriesByProject($projectID);
        $branches            = $this->project->getBranchesByProject($projectID);
        $linkedProducts      = $this->loadModel('product')->getProducts($projectID, 'all', '', true);
        $projectBranches     = $this->project->getBranchGroup($projectID, array_keys($linkedProducts));
        $plans               = $this->loadModel('productplan')->getGroupByProduct(array_keys($linkedProducts), 'skipParent|unexpired');
        $withProgram         = $this->config->systemMode == 'ALM';
        $allProducts         = $this->program->getProductPairs($project->parent, 'all', 'noclosed', '', 0, $withProgram);

        $unmodifiableProducts     = array();
        $unmodifiableMainBranches = array();
        $unmodifiableBranches     = array();
        foreach($linkedProducts as $productID => $linkedProduct)
        {
            if(!isset($allProducts[$productID])) $allProducts[$productID] = $linkedProduct->name;
            foreach($branches[$productID] as $branchID => $branch)
            {
                $linkedBranchList[$branchID] = $branchID;

                if(!isset($productPlans[$productID])) $productPlans[$productID] = isset($plans[$productID][BRANCH_MAIN]) ? $plans[$productID][BRANCH_MAIN] : array();
                $productPlans[$productID] += isset($plans[$productID][$branchID]) ? $plans[$productID][$branchID] : array();

                if(!empty($projectStories[$productID][$branchID]) || !empty($projectBranches[$productID][$branchID]))
                {
                    if($branchID == BRANCH_MAIN) $unmodifiableMainBranches[$productID] = $branchID;
                    array_push($unmodifiableProducts, $productID);
                    array_push($unmodifiableBranches, $branchID);
                }
            }
        }

        $productPlansOrder = array();
        foreach($productPlans as $productID => $plan)
        {
            $orderPlans    = $this->loadModel('productPlan')->getListByIds(array_keys($plan), true);
            $orderPlansMap = array_keys($orderPlans);
            foreach($orderPlansMap as $planMapID)
            {
                $productPlansOrder[$productID][$planMapID] = $productPlans[$productID][$planMapID];
            }
        }

        $this->view->title      = $this->lang->project->edit;

        $this->view->PMUsers                  = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst',  $project->PM);
        $this->view->users                    = $this->user->getPairs('noclosed|nodeleted');
        $this->view->project                  = $project;
        $this->view->programList              = $this->program->getParentPairs();
        $this->view->program                  = $this->program->getByID($project->parent);
        $this->view->projectID                = $projectID;
        $this->view->allProducts              = array('0' => '') + $allProducts;
        $this->view->multiBranchProducts      = $this->product->getMultiBranchPairs();
        $this->view->productPlans             = array_filter($productPlansOrder);
        $this->view->linkedProducts           = $linkedProducts;
        $this->view->branches                 = $branches;
        $this->view->executions               = $this->loadModel('execution')->getPairs($projectID);
        $this->view->unmodifiableProducts     = $unmodifiableProducts;
        $this->view->unmodifiableBranches     = $unmodifiableBranches;
        $this->view->unmodifiableMainBranches = $unmodifiableMainBranches;
        $this->view->branchGroups             = $this->loadModel('branch')->getByProducts(array_keys($linkedProducts), 'noclosed', $linkedBranchList);
        $this->view->URSRPairs                = $this->loadModel('custom')->getURSRPairs();
        $this->view->parentProject            = $parentProject;
        $this->view->parentProgram            = $this->program->getByID($project->parent);
        $this->view->availableBudget          = $this->program->getBudgetLeft($parentProject) + (float)$project->budget;
        $this->view->budgetUnitList           = $this->project->getBudgetUnitList();
        $this->view->model                    = $project->model;
        $this->view->disableModel             = $this->project->checkCanChangeModel($projectID, $project->model) ? '' : 'disabled';
        $this->view->teamMembers              = $this->user->getTeamMemberPairs($projectID, 'project');

        $this->display();
    }

    /**
     * Get copy project and send variables to create page.
     *
     * @param  int $copyProjectID
     * @access protected
     * @return void
     */
    private function getCopyProject(int $copyProjectID): object
    {
        $copyProject = $this->project->getByID($copyProjectID);
        $products    = $this->product->getProducts($copyProjectID);

        foreach($products as $product)
        {
            $branches = implode(',', $product->branches);
            $copyProject->productPlans[$product->id] = $this->loadModel('productplan')->getPairs($product->id, $branches, 'noclosed', true);
        }

        $this->view->branchGroups = $this->loadModel('branch')->getByProducts(array_keys($products), 'noclosed');
        $this->view->products     = $products;
        $this->view->copyProject  = $copyProject;

        return $copyProject;
    }

    /**
     * Append extras data to post data.
     *
     * @param  object $postData
     * @access protected
     * @return int|object
     */
    protected function prepareStartExtras(object $postData): object
    {
        return $postData->add('status', 'doing')
            ->add('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', helper::now())
            ->get();
    }

    /**
     * Append extras data to post data.
     *
     * @param  int    $iprojectID
     * @param  object $postData
     *
     * @access protected
     * @return object
     */
    protected function prepareSuspendExtras(int $projectID, object $postData): object
    {
        $editorIdList = $this->config->project->editor->suspend['id'];

        return $postData->add('id', $projectID)
            ->setDefault('status', 'suspended')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setDefault('suspendedDate', helper::today())
            ->stripTags($editorIdList, $this->config->allowedTags)
            ->get();
    }

    /**
     * Append extras data to post data.
     *
     * @param  int    $iprojectID
     * @param  object $postData
     *
     * @access protected
     * @return object
     */
    protected function prepareClosedExtras(int $projectID, object $postData): object
    {
        $editorIdList = $this->config->project->editor->suspend['id'];

        return  $postData->add('id', $projectID)
            ->setDefault('status', 'closed')
            ->setDefault('closedBy', $this->app->user->account)
            ->setDefault('closedDate', helper::now())
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->stripTags($editorIdList, $this->config->allowedTags)
            ->get();
    }

    /**
     * Send variables to view page.
     *
     * @param  object $project
     * @access protected
     *
     * @return void
     */
    protected function buildStartForm(object $project): void
    {
        $this->view->title   = $this->lang->project->start;
        $this->view->project = $project;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $project->id);
        $this->display();
    }

    /**
     * After starting the project, do other operations.
     *
     * @param  object $project
     * @param  array  $changes
     * @param  string $comment
     *
     * @access protected
     * @return void
     */
    protected function responseAfterStart(object $project, array $changes, string $comment): void
    {
        if($comment != '' || !empty($changes))
        {
            $actionID = $this->loadModel('action')->create('project', $project->id, 'Started', $comment);
            $this->action->logHistory($actionID, $changes);
        }

        $this->loadModel('common')->syncPPEStatus($project->id);

        $this->executeHooks($project->id);
    }

    /**
     * After suspending the project, do other operations.
     *
     * @param  int    $projectID
     * @param  array  $changes
     * @param  string $comment
     *
     * @access protected
     * @return void
     */
    protected function responseAfterSuspend(int $projectID, array $changes, string $comment): void
    {
        if($comment != '' || !empty($changes))
        {
            $actionID = $this->loadModel('action')->create('project', $projectID, 'Suspended', $comment);
            $this->action->logHistory($actionID, $changes);
        }

        $this->loadModel('common')->syncPPEStatus($projectID);

        $this->executeHooks($projectID);
    }

    /**
     * Send variables to suspend page.
     *
     * @param  int $projectID
     * @access protected
     */
    protected function buildSuspendForm(int $projectID): void
    {
        $this->view->title   = $this->lang->project->suspend;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->view->project = $this->project->getByID($projectID);
        $this->display();
    }

    /**
     * After closing the project, do other operations.
     *
     * @param  int    $projectID
     * @param  array  $changes
     * @param  string $comment
     *
     * @access protected
     * @return void
     */
    protected function responseAfterClose(int $projectID, array $changes, string $comment): void
    {
        if($comment != '' || !empty($changes))
        {
            $actionID = $this->loadModel('action')->create('project', $projectID, 'Closed', $comment);
            $this->action->logHistory($actionID, $changes);
        }

        $this->loadModel('common')->syncPPEStatus($projectID);

        $this->executeHooks($projectID);
    }

    /**
     * Send variables to close page.
     *
     * @param  int $projectID
     * @access protected
     *
     * @return void
     */
    protected function buildClosedForm(int $projectID): void
    {
        $this->view->title   = $this->lang->project->close;
        $this->view->users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->project = $this->project->getByID($projectID);
        $this->view->actions = $this->loadModel('action')->getList('project', $projectID);
        $this->display();
    }

    /**
     * 获取项目下拉选择框选项.
     * Get project drop menu.
     *
     * @param  int        $projectID
     * @param  int        $module
     * @param  int        $method
     * @access protected
     * @return void
     */
    protected function getDropMenu(int $projectID, string $module, string $method) :void
    {
        $this->loadModel('program');

        $programs        = array();
        $orderedProjects = array();

        /* Query user's project and program. */
        $projects = $this->project->getListByCurrentUser();
        $programs = $this->program->getPairs(true);

        /* Create the link from module,method. */
        $link = $this->project->getProjectLink($module, $method, $projectID);

        /* Generate project tree. */
        foreach($projects as $project)
        {
            $project->parent = $this->program->getTopByID($project->parent);
            $project->parent = isset($programs[$project->parent]) ? $project->parent : $project->id;

            $orderedProjects[$project->parent][] = $project;
        }

        $this->view->link      = $link;
        $this->view->projectID = $projectID;
        $this->view->projects  = $orderedProjects;
        $this->view->module    = $module;
        $this->view->method    = $method;
        $this->view->programs  = $programs;

        $this->display();
    }

    /**
     * Send variables to activate page.
     *
     * @param  object $project
     * @access protected
     *
     * @return void
     */
    protected function buildActivateForm(object $project): void
    {
        $newBegin = date('Y-m-d');
        $dateDiff = helper::diffDate($newBegin, $project->begin);
        $dateTime = (int)(strtotime($project->end) + $dateDiff * 24 * 3600);
        $newEnd   = date('Y-m-d', $dateTime);

        $this->view->title      = $this->lang->project->activate;
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->loadModel('action')->getList('project', $project->id);
        $this->view->newBegin   = $newBegin;
        $this->view->newEnd     = $newEnd;
        $this->view->project    = $project;
        $this->display();
    }

    /**
     * After activateing the project, do other operations.
     *
     * @param  int    $projectID
     * @param  array  $changes
     *
     * @access protected
     * @return void
     */
    protected function responseAfterActivate(int $projectID, array $changes): void
    {
        if($this->post->comment != '' || !empty($changes))
        {
            $actionID = $this->loadModel('action')->create('project', $projectID, 'Activated', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
        }

        $this->executeHooks($projectID);
    }

    /**
     * Append extras data to post data.
     *
     * @param  int    $iprojectID
     * @param  object $postData
     *
     * @access protected
     * @return object
     */
    protected function prepareActivateExtras(int $projectID, object $postData): object
    {
        $rawdata      = $postData->rawdata;
        $oldProject   = $this->project->getByID($projectID);
        $editorIdList = $this->config->project->editor->activate['id'];

        return $postData->add('id', $projectID)
            ->setDefault('realEnd', $oldProject->realEnd)
            ->setDefault('status', 'doing')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setIF($rawdata->begin == '0000-00-00', 'begin', '')
            ->setIF($rawdata->end   == '0000-00-00', 'end', '')
            ->setIF(!helper::isZeroDate($oldProject->realBegan), 'realBegan', helper::today())
            ->stripTags($editorIdList, $this->config->allowedTags)
            ->get();
    }

    /**
     * 从项目中删除所有关联的执行。
     * removes all associated executions from the be deleted project
     *
     * @param  array $executionIdList
     *
     * @access protected
     * @return void
     */
    protected function removeAssociatedExecutions(array $executionIdList): void
    {
        $this->project->deleteByTableName(TABLE_EXECUTION, array_keys($executionIdList));
        foreach($executionIdList as $executionID => $execution) $this->loadModel('action')->create('execution', $executionID, 'deleted', '', ACTIONMODEL::CAN_UNDELETED);
        $this->loadModel('user')->updateUserView($executionIdList, 'sprint');
    }

    /**
     * 从项目中删除所有关联的产品。
     * removes all associated products from the be deleted project
     *
     * @param  object $project
     *
     * @access protected
     * @return void
     */
    protected function removeAssociatedProducts(object $project): void
    {
        /* Delete shadow product.*/
        if(!$project->hasProduct)
        {
            $productID = $this->loadModel('product')->getProductIDByProject($project->id);
            $this->project->deleteByTableName('zt_product', $productID);
        }
    }

    /**
     * 管理项目的关联产品:构建数据，更新产品变动，记录日志。
     * Manage project related products: build data, update product changes, and record actions.
     *
     * @param  int       $projectID
     * @param  object    $project
     * @param  array     $IdList
     * @access protected
     * @return bool
     */
    protected function updateLinkedProducts(int $projectID, object $project, array $IdList): bool
    {
        /* 获取旧产品数据，并更新。*/
        /* Get old product data and update it. */
        $formerProducts = $this->loadModel('product')->getProducts($projectID);
        $this->project->updateProducts($projectID);
        if(dao::isError()) return false;

        /* 如果关联产品新增或删除，记录动态。*/
        /* If add or delete associated products and record their dynamics. */
        $currentProducts = $this->product->getProducts($projectID);
        $formerIds       = array_keys($formerProducts);
        $currentIds      = array_keys($currentProducts);
        $changes         = array_merge(array_diff($formerIds, $currentIds), array_diff($currentIds, $formerIds));
        if($changes) $this->loadModel('action')->create('project', $projectID, 'Managed', '', !empty($this->post->products) ? implode(',', $this->post->products) : '');

        /* 如果是无迭代项目，更新关联产品。*/
        /* IF multiple is 0 Update associated products. */
        if(empty($project->multiple))
        {
            $executionID = $this->loadModel('execution')->getNoMultipleID($projectID);
            if($executionID) $this->execution->updateProducts($executionID);
        }

        /* 如果是瀑布项目单套阶段，更新关联产品。*/
        /* If it is a single stage of the waterfall project, update associated products. */
        if($project->stageBy == 'project')
        {
            foreach($IdList as $executionID)
            {
                $this->execution->updateProducts($executionID);
                if($changes) $this->loadModel('action')->create('execution', $executionID, 'Managed', '', implode(',', $currentIds));
            }
        }

        /* 如果有迭代并且是非瀑布项目，记录关联产品执行到action表。*/
        /* If it is multiple project and model isn't waterfall project, record to table action. */
        if($project->multiple && $project->model != 'waterfall' && $project->model != 'waterfallplus')
        {
            $this->recordExecutionsOfUnlinkedProducts($formerProducts, $currentIds, $IdList);
        }

        return true;
    }

    /**
     * 记录取消关联产品的执行到action表。
     * Record the execution of disassociated products to the action table.
     *
     * @param  array     $formerProducts
     * @param  array     $selectedIds
     * @param  array     $executionIdList
     * @access protected
     * @return void
     */
    protected function recordExecutionsOfUnlinkedProducts(array $formerProducts, array $selectedIds, array $executionIdList): void
    {
        $executionProductGroup = $this->loadModel('project')->getExecutionProductGroup($executionIdList); //项目下所有执行对应的关联产品。
        $unlinkedProductIds    = array_diff(array_keys($formerProducts), $selectedIds);                   //取消关联的产品。
        if(!empty($unlinkedProductIds))
        {
            $unlinkedProductPairs = array(); //取消关联的产品id->name键值对。
            foreach($unlinkedProductIds as $productID) $unlinkedProductPairs[$productID] = $formerProducts[$productID]->name;

            $executions = array(); //取消关联产品的执行
            foreach($executionProductGroup as $executionID => $products) //遍历执行对应关联产品键值对。
            {
                $unlinkedExecutionProducts = array_intersect_key($unlinkedProductPairs, $products); //获取执行中解除的关联产品。
                if($unlinkedExecutionProducts) $executions[$executionID] = $unlinkedExecutionProducts;
            }

            foreach($executions as $executionID => $unlinkedExecutionProducts)
            {
                $this->loadModel('action')->create('execution', $executionID, 'unlinkproduct', '', implode(',', $unlinkedExecutionProducts));
            }
        }
    }

    /**
     * 提取关联用户故事无法移除的项目产品
     * Extract related stories cannot be removed product.
     *
     * @param  int    $projectID
     * @param  object $project
     *
     * @access protected
     * @return void
     */
    protected function extractUnModifyForm(int $projectID, object $project): void
    {
        $linkedBranches      = array();
        $linkedBranchIdList  = array();
        $branches            = $this->project->getBranchesByProject($projectID);
        $linkedProductIdList = empty($branches) ? '' : array_keys($branches);
        $allProducts         = $this->loadModel('program')->getProductPairs($project->parent, 'all', 'noclosed', $linkedProductIdList);
        $linkedProducts      = $this->loadModel('product')->getProducts($projectID, 'all', '', true, $linkedProductIdList);
        $projectStories      = $this->project->getStoriesByProject($projectID);
        $projectBranches     = $this->project->getBranchGroup($projectID, array_keys($linkedProducts));

        /* If the story of the product which linked the project,don't allow to remove the product. */
        $unmodifiableProducts     = array();
        $unmodifiableBranches     = array();
        $unmodifiableMainBranches = array();
        foreach($linkedProducts as $productID => $linkedProduct)
        {
            $linkedBranches[$productID] = array();
            foreach($branches[$productID] as $branchID => $branch)
            {
                $linkedBranches[$productID][$branchID] = $branchID;
                $linkedBranchIdList[$branchID] = $branchID;

                if(!empty($projectStories[$productID][$branchID]) || !empty($projectBranches[$productID][$branchID]))
                {
                    if($branchID == BRANCH_MAIN) $unmodifiableMainBranches[$productID] = $branchID;
                    array_push($unmodifiableProducts, $productID);
                    array_push($unmodifiableBranches, $branchID);
                }
            }
        }

        /* Build the product from other linked products. */
        if($this->config->systemMode == 'ALM') $this->buildProductForm($project, $allProducts, $linkedBranchIdList, $linkedBranches, $linkedProducts);

        $this->view->linkedProducts           = $linkedProducts;
        $this->view->unmodifiableProducts     = $unmodifiableProducts;
        $this->view->unmodifiableBranches     = $unmodifiableBranches;
        $this->view->unmodifiableMainBranches = $unmodifiableMainBranches;
    }

    /**
     * 初始化项目集下其他关联产品中当前产品
     * Build the current product under the projectprogram.
     *
     * @param  object $project
     * @param  array  $allProducts
     * @param  array  $linkedBranchIdList
     * @param  array  $linkedBranches
     * @param  array  $linkedProducts
     *
     * @access protected
     * @return void
     */
    protected function buildProductForm(object $project, array $allProducts, array $linkedBranchIdList, array $linkedBranches, array $linkedProducts): void
    {
        $branchGroups           = $this->loadModel('branch')->getByProducts(array_keys($allProducts), 'ignoreNormal|noclosed', $linkedBranchIdList);
        $topProgramID           = $project->parent ? $this->program->getTopByPath($project->path) : 0;
        $productsGroupByProgram = $this->loadModel('product')->getProductsGroupByProgram();

        $currentProducts = array();
        $otherProducts   = array();
        foreach($productsGroupByProgram as $programID => $programProducts)
        {
            if($programID != $topProgramID)
            {
                $otherProducts = $this->getOtherProducts($programProducts, $branchGroups, $linkedBranches, $linkedProducts);
            }
            else
            {
                $currentProducts += $programProducts;
            }
        }

        $this->view->currentProducts = $currentProducts;
        $this->view->otherProducts   = $otherProducts;
        $this->view->branchGroups    = $branchGroups;
        $this->view->linkedBranches  = $linkedBranches;
        $this->view->linkedProducts  = $linkedProducts;
        $this->view->allProducts     = $allProducts;
        $this->view->allBranches     = $this->loadModel('branch')->getByProducts(array_keys($allProducts), 'ignoreNormal');
    }

    /**
     * 获取其他的关联产品
     * Get other products under the projectprogram.
     *
     * @param  array     $programProducts
     * @param  array     $branchGroups
     * @param  array     $linkedBranches
     * @param  array     $linkedProducts
     *
     * @access protected
     * @return array
     */
    protected function getOtherProducts(array $programProducts, array $branchGroups, array $linkedBranches, array $linkedProducts): array
    {
        $otherProducts = array();
        foreach($programProducts as $productID => $productName)
        {
            if(!empty($branchGroups[$productID]))
            {
                foreach($branchGroups[$productID] as $branchID => $branchName)
                {
                    if(isset($linkedProducts[$productID]) && isset($linkedBranches[$productID][$branchID])) continue;
                    $otherProducts["{$productID}_{$branchID}"] = $productName . '_' . $branchName;
                }
            }
            else
            {
                if(isset($linkedProducts[$productID])) continue;
                $otherProducts[$productID] = $productName;
            }
        }

        return $otherProducts;
    }

    /**
     * 根据当前所在模块更新二级菜单
     * set project menu
     *
     * @param  int $projectID
     * @param  int $project
     * @access protected
     * @return void
     */
    protected function setProjectMenu(int $projectID, int $projectParent): void
    {
        if($this->app->tab == 'program')
        {
            $this->loadModel('program')->setMenu($projectParent);
        }
        elseif($this->app->tab == 'project')
        {
            $this->project->setMenu($projectID);
        }
    }

    /**
     * 处理项目列表展示数据。
     * Process project list display data.
     *
     * @param  array     $projectList
     * @access protected
     * @return array
     */
    protected function processProjectListData(array $projectList): array
    {
        $userList = $this->dao->select('account,realname,avatar')->from(TABLE_USER)->fetchAll('account');

        $this->loadModel('story');
        $this->loadModel('execution');
        foreach($projectList as $project)
        {
            $project = $this->project->formatDataForList($project, $userList);

            $projectStories = $this->story->getExecutionStoryPairs($project->id);
            $project->storyCount = count($projectStories);

            $executions = $this->execution->getStatData($project->id, 'all');
            $project->executionCount = count($executions);

            $project->from    = 'project';
            $project->actions = $this->project->buildActionList($project);
        }

        return array_values($projectList);
    }

    /**
     * 处理版本列表展示数据。
     * Process build list display data.
     *
     * @param  array     $builds
     * @param  int       $projectID
     * @access protected
     * @return object[]
     */
    protected function processBuildListData(array $builds, int $projectID): array
    {
        $this->loadModel('build');
        $this->loadModel('branch');

        $showBranch    = false;
        $productIdList = array();
        foreach($builds as $build) $productIdList[$build->product] = $build->product;

        /* Get branch name. */
        $branchGroups = $this->branch->getByProducts($productIdList);
        foreach($builds as $build)
        {
            $build->branchName = '';
            if(isset($branchGroups[$build->product]))
            {
                $showBranch  = true;
                $branchPairs = $branchGroups[$build->product];
                foreach(explode(',', trim($build->branch, ',')) as $branchID)
                {
                    if(isset($branchPairs[$branchID])) $build->branchName .= "{$branchPairs[$branchID]},";
                }
                $build->branchName = trim($build->branchName, ',');

                if(empty($build->branchName) and empty($build->builds)) $build->branchName = $this->lang->branch->main;
            }
            $build->actions = $this->build->buildActionList($build, 0, 'projectbuild');
        }

        /* Set data table column. */
        $project = $this->project->getByID($projectID);
        if(!$project->hasProduct) unset($this->config->build->dtable->fieldList['product']);
        if(!$showBranch || !$project->hasProduct) unset($this->config->build->dtable->fieldList['branch']);
        if(!$project->multiple) unset($this->config->build->dtable->fieldList['execution']);
        $this->config->build->dtable->fieldList['name']['link'] = helper::createLink('projectbuild', 'view', 'buildID={id}');
        $this->config->build->dtable->fieldList['execution']['title'] = zget($this->lang->project->executionList, $project->model);

        return array_values($builds);
    }

    /**
     * 构建项目团队成员信息。
     * Build project team member information.
     *
     * @param  array  $currentMembers
     * @param  array  $members2Import
     * @param  array  $deptUsers
     * @param  int    $days
     * @access public
     * @return array
     */
    public function buildMembers(array $currentMembers, array $members2Import, array $deptUsers, int $days): array
    {
        $teamMembers = array();
        foreach($currentMembers as $account => $member)
        {
            $member->memberType = 'default';
            $teamMembers[$account] = $member;
        }

        $roles = $this->loadModel('user')->getUserRoles(array_keys($deptUsers));
        foreach($deptUsers as $deptAccount => $userName)
        {
            if(isset($currentMembers[$deptAccount]) || isset($members2Import[$deptAccount])) continue;

            $deptMember = new stdclass();
            $deptMember->memberType = 'dept';
            $deptMember->account    = $deptAccount;
            $deptMember->role       = zget($roles, $deptAccount, '');
            $deptMember->days       = $days;
            $deptMember->hours      = $this->config->execution->defaultWorkhours;
            $deptMember->limited    = 'no';

            $teamMembers[$deptAccount] = $deptMember;
        }

        foreach($members2Import as $account => $member2Import)
        {
            $member2Import->memberType = 'import';
            $member2Import->days       = $days;
            $member2Import->limited    = 'no';
            $teamMembers[$account] = $member2Import;
        }

        for($j = 0; $j < 5; $j ++)
        {
            $newMember = new stdclass();
            $newMember->memberType = 'add';
            $newMember->account    = '';
            $newMember->role       = '';
            $newMember->days       = $days;
            $newMember->hours      = $this->config->execution->defaultWorkhours;
            $newMember->limited    = 'no';

            $teamMembers[] = $newMember;
        }

        return $teamMembers;
    }
}

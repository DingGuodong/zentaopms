<?php
declare(strict_types=1);
/**
 * The browseStoryConcept view file of custom module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     custom
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('defaultKey', $config->custom->URSR);

$canCreate = hasPriv('custom', 'setstoryconcept');
if($canCreate) $createItem = array('icon' => 'plus', 'class' => 'primary', 'text' => $lang->custom->setStoryConcept, 'url' => $this->createLink('custom', 'setstoryconcept'), 'data-toggle' => 'modal');

div
(
    setClass('story-concept-panel'),
    div
    (
        setClass('panel-header flex-auto'),
        div
        (
            setClass('flex-auto article-h2'),
            $lang->custom->product->fields['browsestoryconcept']
        ),
        toolbar
        (
            !empty($createItem) ? item(set($createItem)) : null,
        ),
    ),
);

$tableData = initTableData($URSRList, $config->custom->browseStoryConcept->dtable->fieldList);
dtable
(
    set::cols($config->custom->browseStoryConcept->dtable->fieldList),
    set::data($tableData),
    set::onRenderCell(jsRaw('window.renderCell')),
);

/* ====== Render page ====== */
render();

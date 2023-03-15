<?php
namespace zin;

$cols = array_values($config->product->all->dtable->fieldList);
foreach($cols as $idx => $col)
{
    if($col['name'] == 'name')
    {
        unset($cols[$idx]['width']);
        $cols[$idx]['minWidth']     = 200;
        $cols[$idx]['nestedToggle'] = true;
        $cols[$idx]['iconRender']   = jsRaw('function(row){return row.data.type === \'program\' ? \'icon-cards-view text-gray\' : \'\'}');
        continue;
    }

    if($col['name'] != 'actions') continue;

    $cols[$idx]['actionsMap'] = array
    (
        'edit'      => array('icon'=> 'icon-edit',         'hint'=> '编辑'),
        'group'     => array('icon'=> 'icon-group',        'hint'=> '团队'),
        'split'     => array('icon'=> 'icon-split',        'hint'=> '添加子项目集'),
        'delete'    => array('icon'=> 'icon-trash',        'hint'=> '删除', 'text' => '删除'),
        'close'     => array('icon'=> 'icon-off',          'hint'=> '关闭'),
        'start'     => array('icon'=> 'icon-start',        'hint'=> '开始'),
        'pause'     => array('icon'=> 'icon-pause',        'text'=> '挂起项目集'),
        'active'    => array('icon'=> 'icon-magic',        'text'=> '激活项目集'),
        'other'     => array('type'=> 'dropdown',          'hint'=> '其他操作', 'caret' => true),
        'link'      => array('icon'=> 'icon-link',         'text'=> '关联产品', 'name' => 'link'),
        'more'      => array('icon'=> 'icon-ellipsis-v',   'hint'=> '更多', 'type' => 'dropdown', 'caret' => false),
        'whitelist' => array('icon'=> 'icon-shield-check', 'text'=> '项目白名单', 'name' => 'whitelist'),
    );
    $cols[$idx]['type']  = 'actions';
    $cols[$idx]['width'] = 128;
}

/* TODO: implements extend fields. */
$extendFields = $this->product->getFlowExtendFields();

$data         = array();
$totalStories = 0;
foreach($productStructure as $programID => $program)
{
    if(isset($programLines[$programID]))
    {
        foreach($programLines[$programID] as $lineID => $lineName)
        {
            if(!isset($program[$lineID]))
            {
                $program[$lineID] = array();
                $program[$lineID]['product']  = '';
                $program[$lineID]['lineName'] = $lineName;
            }
        }
    }

    /* ALM mode with more data. */
    if(isset($program['programName']) and $config->systemMode == 'ALM')
    {
        $item = new stdClass();

        $item->programPM = '';
        if(!empty($program['programPM']))
        {
            /* TODO generate avatar and link. */
            $programPM = $program['programPM'];
            $userName  = zget($users, $programPM);
            // echo html::smallAvatar(array('avatar' => $usersAvatar[$programPM], 'account' => $programPM, 'name' => $userName), 'avatar-circle avatar-top avatar-' . zget($userIdPairs, $programPM));

            $userID = isset($userIdPairs[$programPM]) ? $userIdPairs[$programPM] : '';
            // echo html::a($this->createLink('user', 'profile', "userID=$userID", '', true), $userName, '', "title='{$userName}' class='iframe' data-width='600'");

            $item->programPM = $userName;
            $item->PO        = $userName;
        }

        $totalStories = $program['finishClosedStories'] + $program['unclosedStories'];

        $item->name             = $program['programName'];
        $item->id               = 'program-' . $programID;
        $item->type             = 'program';
        $item->level            = 1;
        $item->asParent         = true;
        $item->programName      = $program['programName'];
        $item->draftStories     = $program['draftStories'];
        $item->activeStories    = $program['activeStories'];
        $item->changingStories  = $program['changingStories'];
        $item->reviewingStories = $program['reviewingStories'];
        $item->totalStories     = ($totalStories == 0 ? 0 : round($program['finishClosedStories'] / $totalStories, 3) * 100) . '%';
        $item->unResolvedBugs   = $program['unResolvedBugs'];
        $item->totalBugs        = (($program['unResolvedBugs'] + $program['fixedBugs']) == 0 ? 0 : round($program['fixedBugs'] / ($program['unResolvedBugs'] + $program['fixedBugs']), 3) * 100) . '%';
        $item->plans            = $program['plans'];
        $item->releases         = $program['releases'];
        /* TODO attach extend fields. */
        $item->actions          = 'close|other:-pause,active|group|-edit|more:delete,link';

        $data[] = $item;
    }

    foreach($program as $lineID => $line)
    {
        /* ALM mode with Product Line. */
        if(isset($line['lineName']) and isset($line['products']) and is_array($line['products']) and $config->systemMode == 'ALM')
        {
            $totalStories = (isset($line['finishClosedStories']) ? $line['finishClosedStories'] : 0) + (isset($line['unclosedStories']) ? $line['unclosedStories'] : 0);

            $item = new stdClass();
            $item->name             = $line['lineName'];
            $item->id               = 'productLine-' . $lineID;
            $item->type             = 'productLine';
            $item->asParent         = true;
            $item->parent           = 'program-' . $programID;
            $item->programName      = $line['lineName'];
            $item->draftStories     = $line['draftStories'];
            $item->activeStories    = $line['activeStories'];
            $item->changingStories  = $line['changingStories'];
            $item->reviewingStories = $line['reviewingStories'];
            $item->totalStories     = ($totalStories == 0 ? 0 : round((isset($line['finishClosedStories']) ? $line['finishClosedStories'] : 0) / $totalStories, 3) * 100) . '%';
            $item->unResolvedBugs   = $line['unResolvedBugs'];
            $item->totalBugs        = ((isset($line['fixedBugs']) and ($line['unResolvedBugs'] + $line['fixedBugs'] != 0)) ? round($line['fixedBugs'] / ($line['unResolvedBugs'] + $line['fixedBugs']), 3) * 100 : 0) . '%';
            $item->plans            = $line['plans'];
            $item->releases         = $line['releases'];
            /* TODO attach extend fields. */
            $item->actions          = 'close|other:-pause,active|group|-edit|more:delete,link';

            $data[] = $item;
        }

        /* Products of Product Line. */
        if(isset($line['products']) and is_array($line['products']))
        {
            foreach($line['products'] as $productID => $product)
            {
                $item = new stdClass();

                if(!empty($product->PO))
                {
                    $item->PO               = zget($users, $product->PO);
                    $item->POAvatar         = $usersAvatar[$product->PO];
                    $item->POAccount        = $product->PO;
                }
                $totalStories = $product->stories['finishClosed'] + $product->stories['unclosed'];

                $item->name             = $product->name; /* TODO replace with <a> */
                $item->id               = $product->id;
                $item->type             = 'project';
                $item->level            = 2;
                $item->programName      = $product->name; /* TODO replace with <a> */
                $item->draftStories     = $product->stories['draft'];
                $item->activeStories    = $product->stories['active'];
                $item->changingStories  = $product->stories['changing'];
                $item->reviewingStories = $product->stories['reviewing'];
                $item->totalStories     = ($totalStories == 0 ? 0 : round($product->stories['finishClosed'] / $totalStories, 3) * 100) . '%';
                $item->unResolvedBugs   = $product->unResolved;
                $item->totalBugs        = (($product->unResolved + $product->fixedBugs) == 0 ? 0 : round($product->fixedBugs / ($product->unResolved + $product->fixedBugs), 3) * 100) . '%';
                $item->plans            = $product->plans;
                $item->releases         = $product->releases;
                $item->parent           = $product->program ? "program-$product->program" : '';
                /* TODO attach extend fields. */
                $item->actions          = 'close|other:-pause,active|group|-edit|more:delete,link';

                $data[] = $item;
            }
        }
    }
}

$footer = array(
    'items' => array(
        array('type' => 'info', 'text' => '共 {recTotal} 项'),
        array('type' => 'info', 'text' => '{page}/{pageTotal}'),
    ),
    'page' => 1,
    'recTotal' => 101,
    'recPerPage' => 10,
    'linkCreator' => '#?page{page}&recPerPage={recPerPage}'
);

featureBar
(
    hasPriv('product', 'batchEdit') ? item
    (
        set::type('checkbox'),
        set::text($lang->product->edit),
        set::checked($this->cookie->editProject)
    ) : NULL,
    item
    (
        set::icon('search'),
        set::text($lang->product->searchStory)
        /* toggle('search-panel') */
    )
);

toolbar
(
    item(set(array
    (
        'text'  => $lang->product->export,
        'icon'  => 'export',
        'class' => 'secondary',
        'url'   => createLink('product', 'export', $browseType, "status=$browseType&orderBy=$orderBy"),
    ))),
    $config->systemMode == 'ALM' ? item(set(array
    (
        'text'  => $lang->product->line,
        'icon'  => 'edit',
        'class' => 'secondary',
        'url'   => createLink('product', 'manageLine', $browseType),
    ))) : NULL,
    item(set(array
    (
        'text'  => $lang->product->create,
        'icon'  => 'plus',
        'class' => 'primary',
        'url'   => createLink('product', 'create')
    )))
);

dtable
(
    set::className('shadow rounded'),
    set::cols($cols),
    set::data($data),
    set::footPager($footer),
    set::nested(true),
    set::footToolbar(array('items' => array(array('size' => 'sm', 'text' => '编辑', 'btnType' => 'primary'))))
);

jsVar('window.$data', $data);

render();

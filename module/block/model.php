<?php
declare(strict_types=1);
/**
 * The model file of block module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class blockModel extends model
{
    /**
     * Check API for ranzhi
     *
     * @param  string $hash
     * @access public
     * @return bool
     */
    public function checkAPI(string $hash): bool
    {
        if(empty($hash)) return false;

        $key = $this->dao->select('value')->from(TABLE_CONFIG)
            ->where('owner')->eq('system')
            ->andWhere('module')->eq('sso')
            ->andWhere('`key`')->eq('key')
            ->fetch('value');

        return $key == $hash;
    }

    /**
     * Get a block by blockID.
     * 根据区块ID获取区块信息.
     *
     * @param  int    $blockID
     * @access public
     * @return object|false
     */
    public function getByID(int $blockID): object|false
    {
        $block = $this->dao->select('*')->from(TABLE_BLOCK)->where('id')->eq($blockID)->fetch();
        if(empty($block)) return false;

        $block->params = json_decode($block->params);
        if(empty($block->params)) $block->params = new stdclass();
        if($block->code == 'html') $block->params->html = $this->loadModel('file')->setImgSize($block->params->html);
        return $block;
    }

    /**
     * Get closed block pairs.
     *
     * @param  string $closedBlock
     * @access public
     * @return array
     */
    public function getClosedBlockPairs(string $closedBlock): array
    {
        $blockPairs = array();
        if(empty($closedBlock)) return $blockPairs;

        foreach(explode(',', $closedBlock) as $block)
        {
            $block = trim($block);
            if(empty($block)) continue;

            list($moduleName, $blockKey) = explode('|', $block);
            if(empty($moduleName))
            {
                if(isset($this->lang->block->$blockKey)) $blockPairs[$block] = $this->lang->block->$blockKey;
                if($blockKey == 'html')    $blockPairs[$block] = 'HTML';
                if($blockKey == 'guide')   $blockPairs[$block] = $this->lang->block->guide;
                if($blockKey == 'dynamic') $blockPairs[$block] = $this->lang->block->dynamic;
                if($blockKey == 'welcome') $blockPairs[$block] = $this->lang->block->welcome;
            }
            else
            {
                $blockName = $blockKey;
                if(isset($this->lang->block->modules[$moduleName]->availableBlocks->$blockKey)) $blockName = $this->lang->block->modules[$moduleName]->availableBlocks->$blockKey;
                if(isset($this->lang->block->modules[$moduleName]->availableBlocks[$blockKey])) $blockName = $this->lang->block->modules[$moduleName]->availableBlocks[$blockKey];
                if(isset($this->lang->block->availableBlocks->$blockKey)) $blockName = $this->lang->block->availableBlocks->$blockKey;
                if(isset($this->lang->block->modules['scrum']['index']->availableBlocks->$blockKey)) $blockName = $this->lang->block->modules['scrum']['index']->availableBlocks->$blockKey;
                if(isset($this->lang->block->modules['waterfall']['index']->availableBlocks->$blockKey)) $blockName = $this->lang->block->modules['waterfall']['index']->availableBlocks->$blockKey;

                $blockPairs[$block]  = isset($this->lang->block->moduleList[$moduleName]) ? "{$this->lang->block->moduleList[$moduleName]}|" : '';
                $blockPairs[$block] .= $blockName;
            }
        }

        return $blockPairs;
    }

    /**
     * Get block list of current user.
     * 获取区块列表.
     *
     * @param  string $module
     * @param  int    $hidden
     * @access public
     * @return int[]|false
     */
    public function getMyDashboard(string $dashboard, int $hidden = 0): array|false
    {
        return $this->blockTao->fetchMyBlocks($dashboard, $hidden);
    }

    /**
     * Get hidden blocks
     * 获取隐藏的区块列表.
     *
     * @param  string $module
     * @access public
     * @return int[]|false
     */
    public function getMyHiddenBlocks(string $dashboard): array|false
    {
        return $this->blockTao->fetchMyBlocks($dashboard, $hidden = 1);
    }

    /**
     * Get max order number by block dashboard.
     * 获取对应仪表盘下区块的最大排序号.
     *
     * @param  string $dashboard
     * @access public
     * @return int
     */
    public function getMaxOrderByDashboard(string $dashboard): int
    {
        $order = $this->dao->select('MAX(`order`) as `order`')->from(TABLE_BLOCK)
            ->where('dashboard')->eq($dashboard)
            ->andWhere('account')->eq($this->app->user->account)
            ->fetch('order');

        return (int)$order;
    }

    /**
     * Get data of welcome block.
     *
     * @access public
     * @return array
     */
    public function getWelcomeBlockData()
    {
        $data = array();

        $tasks = $this->dao->select("count(distinct t1.id) as tasks, count(distinct if(t1.status = 'done', 1, null)) as doneTasks")->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on("t1.project = t2.id")
            ->leftJoin(TABLE_EXECUTION)->alias('t3')->on("t1.execution = t3.id")
            ->leftJoin(TABLE_TASKTEAM)->alias('t4')->on("t4.task = t1.id and t4.account = '{$this->app->user->account}'")
            ->where("(t1.assignedTo = '{$this->app->user->account}' or (t1.mode = 'multi' and t4.`account` = '{$this->app->user->account}' and t1.status != 'closed' and t4.status != 'done') )")
            ->andWhere('(t2.status')->ne('suspended')
            ->orWhere('t3.status')->ne('suspended')
            ->markRight(1)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t3.deleted')->eq('0')
            ->andWhere('t1.status')->notin('closed,cancel')
            ->beginIF(!$this->app->user->admin)->andWhere('t1.execution')->in($this->app->user->view->sprints)->fi()
            ->beginIF($this->config->vision)->andWhere('t1.vision')->eq($this->config->vision)->fi()
            ->beginIF($this->config->vision)->andWhere('t3.vision')->eq($this->config->vision)->fi()
            ->fetch();

        $data['tasks']     = isset($tasks->tasks)     ? $tasks->tasks : 0;
        $data['doneTasks'] = isset($tasks->doneTasks) ? $tasks->doneTasks : 0;

        $data['bugs']       = (int)$this->dao->select('count(*) AS count')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on("t1.product = t2.id")
            ->where('t1.assignedTo')->eq($this->app->user->account)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.status')->ne('closed')
            ->andWhere('t2.deleted')->eq(0)
            ->fetch('count');
        $data['stories']    = (int)$this->dao->select('count(*) AS count')->from(TABLE_STORY)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product=t2.id')
            ->where('t1.assignedTo')->eq($this->app->user->account)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.type')->eq('story')
            ->fetch('count');
        $data['executions'] = (int)$this->dao->select('count(*) AS count')->from(TABLE_EXECUTION)
            ->where('status')->notIN('done,closed')
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetch('count');
        $data['products']   = (int)$this->dao->select('count(*) AS count')->from(TABLE_PRODUCT)
            ->where('status')->ne('closed')
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetch('count');

        $today = date('Y-m-d');
        $data['delayTask'] = (int)$this->dao->select('count(t1.id) AS count')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on("t1.project = t2.id")
            ->leftJoin(TABLE_EXECUTION)->alias('t3')->on("t1.execution = t3.id")
            ->where('t1.assignedTo')->eq($this->app->user->account)
            ->andWhere('(t2.status')->ne('suspended')
            ->orWhere('t3.status')->ne('suspended')
            ->markRight(1)
            ->andWhere('t1.status')->in('wait,doing')
            ->andWhere('t1.deadline')->notZeroDate()
            ->andWhere('t1.deadline')->lt($today)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->fetch('count');
        $data['delayBug'] = (int)$this->dao->select('count(*) AS count')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product=t2.id')
            ->where('t1.assignedTo')->eq($this->app->user->account)
            ->andWhere('t1.status')->eq('active')
            ->andWhere('t1.deadline')->notZeroDate()
            ->andWhere('t1.deadline')->lt($today)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->fetch('count');
        $data['delayProject'] = (int)$this->dao->select('count(*) AS count')->from(TABLE_PROJECT)
            ->where('status')->in('wait,doing')
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('end')->lt($today)
            ->andWhere('deleted')->eq(0)
            ->fetch('count');

        return $data;
    }

    /**
     * Get the total estimated man hours required.
     *
     * @param  array $storyID
     * @access public
     * @return string
     */
    public function getStorysEstimateHours($storyID)
    {
        return $this->dao->select('count(estimate) as estimate')->from(TABLE_STORY)->where('id')->in($storyID)->fetch('estimate');
    }

    /**
     * Get zentao.net data.
     *
     * @param  string $minTime
     * @access public
     * @return array
     */
    public function getZentaoData($minTime = '')
    {
        return $this->dao->select('type,params')->from(TABLE_BLOCK)
            ->where('account')->eq('system')
            ->andWhere('vision')->eq('rnd')
            ->andWhere('module')->eq('zentao')
            ->beginIF($minTime)->andWhere('source')->ge($minTime)->fi()
            ->andWhere('type')->in('plugin,patch,publicclass,news')
            ->fetchPairs('type');
    }

    /**
     * 根据区块索引获取排序靠后的一个区块ID。
     * Get my block id by block code,
     * 
     * @param  string    $dashboard
     * @param  string    $module
     * @param  string    $code
     * @access public
     * @return int|false
     */
    public function getSpecifiedBlockID(string $dashboard, string $module, string $code): int|false
    {
        $blockID = $this->dao->select('id')->from(TABLE_BLOCK)
            ->where('account')->eq($this->app->user->account)
            ->andWhere('dashboard')->eq($dashboard)
            ->andWhere('module')->eq($module)
            ->andWhere('code')->eq($code)
            ->orderBy('order_desc')
            ->limit(1)
            ->fetch('id');

        return $blockID ? $blockID : false;
    }

    /**
     * Create a block.
     *
     * @param  object $formData
     * @access public
     * @return void
     */
    public function create(object $formData): int|false
    {
        $this->blockTao->insert($formData);
        if(dao::isError()) return false;

        $blockID = $this->dao->lastInsertID();
        $this->loadModel('score')->create('block', 'set');

        return (int)$blockID;
    }

    /**
     * Update a block.
     *
     * @param  object $formData
     * @access public
     * @return void
     */
    public function update(object $formData): int|false
    {
        $this->dao->update(TABLE_BLOCK)->data($formData)
            ->where('id')->eq($formData->id)
            ->autoCheck()
            ->batchCheck($this->config->block->edit->requiredFields, 'notempty')
            ->exec();

        if(dao::isError()) return false;

        $this->loadModel('score')->create('block', 'set');

        return (int)$formData->id;
    }

    /**
     * Reset dashboard blocks.
     *
     * @param  string  $dashboard
     * @access public
     * @return bool
     */
    public function reset(string $dashboard): bool
    {
        $this->dao->delete()->from(TABLE_BLOCK)
            ->where('dashboard')->eq($dashboard)
            ->andWhere('vision')->eq($this->config->vision)
            ->andWhere('account')->eq($this->app->user->account)
            ->exec();

        $this->dao->delete()->from(TABLE_CONFIG)
            ->where('module')->eq($dashboard)
            ->andWhere('vision')->eq($this->config->vision)
            ->andWhere('owner')->eq($this->app->user->account)
            ->andWhere('`key`')->eq('blockInited')
            ->exec();

        return true;
    }

    /**
     * Hidden a block.
     *
     * @param  int $blockID
     * @access public
     * @return bool
     */
    public function hidden(int $blockID): bool
    {
        $this->dao->update(TABLE_BLOCK)->set('hidden')->eq(1)
            ->where('id')->eq($blockID)
            ->andWhere('account')->eq($this->app->user->account)
            ->andWhere('vision')->eq($this->config->vision)
            ->exec();

        return true;
    }

    /**
     * 关闭一个区块。
     * Close a block.
     *
     * @param  object $block
     * @access public
     * @return bool
     */
    public function closeBlock(object $block): bool
    {
        $this->dao->delete()->from(TABLE_BLOCK)
            ->where('module')->eq($block->module)
            ->andWhere('code')->eq($block->code)
            ->exec();
        return true;
    }

    /**
     * Delete a block.
     *
     * @param  int    $blockID
     * @access public
     * @return bool
     */
    public function deleteBlock(int $blockID = 0): bool
    {
        $this->dao->delete()->from(TABLE_BLOCK)
            ->where('id')->eq($blockID)
            ->andWhere('account')->eq($this->app->user->account)
            ->andWhere('vision')->eq($this->config->vision)
            ->exec();

        return true;
    }

    /**
     * Set block order.
     *
     * @param  int    $blockID
     * @param  int    $order
     * @access public
     * @return bool
     */
    public function setOrder(int $blockID, int $order): bool
    {
        $this->dao->update(TABLE_BLOCK)->set('order')->eq($order)->where('id')->eq($blockID)->exec();
        return true;
    }

    /**
     * Init block when account use first.
     * 用户首次加载时初始化区块数据.
     *
     * @param  string $dashboard
     * @access public
     * @return bool
     */
    public function initBlock(string $dashboard): bool
    {
        if(!$dashboard) return false;

        $flow    = isset($this->config->global->flow) ? $this->config->global->flow : 'full';
        $account = $this->app->user->account;
        $vision  = $this->config->vision;

        $blocks = $dashboard == 'my' ? $this->lang->block->default[$flow][$dashboard] : $this->lang->block->default[$dashboard];

        foreach($blocks as $index => $block)
        {
            $block['account']   = $account;
            $block['dashboard'] = $dashboard;
            $block['order']     = $index;
            $block['params']    = isset($block['params']) ? helper::jsonEncode($block['params']) : '';
            $block['vision']    = $this->config->vision;

            $this->blockTao->insert($block);
        }
        if(dao::isError()) return false;

        /* Mark this app has init. */
        $this->loadModel('setting')->setItem("$account.$dashboard.common.blockInited@$vision", '1');
        $this->loadModel('setting')->setItem("$account.$dashboard.block.initVersion", $this->config->block->version);

        return true;
    }

    /**
     * Check whether long block.
     *
     * @param  object    $block
     * @access public
     * @return bool
     */
    public function isLongBlock(object $block): bool
    {
        return (!empty($block->grid) and $block->grid >= 6) ? true : false;
    }

    /**
     * Set zentao data.
     *
     * @param  string $type
     * @param  string $params
     * @access public
     * @return void
     */
    public function setZentaoData($type = 'patch', $params = '')
    {
        $data = new stdclass();
        $data->account = 'system';
        $data->vision  = 'rnd';
        $data->module  = 'zentao';
        $data->type    = $type;
        $data->source  = date('Y-m-d');
        $data->params  = json_encode($params);

        $this->dao->replace(TABLE_BLOCK)->data($data)->exec();
    }

    /**
     * 获取区块是否已经初始化.
     * Fetch block is initiated or not.
     *
     * @param  string $dashboard
     * @return string
     */
    public function fetchBlockInitStatus(string $dashboard): string
    {
        return $this->dao->select('*')->from(TABLE_CONFIG)
            ->where('module')->eq($dashboard)
            ->andWhere('owner')->eq($this->app->user->account)
            ->andWhere('`section`')->eq('common')
            ->andWhere('`key`')->eq('blockInited')
            ->andWhere('vision')->eq($this->config->vision)
            ->fetch('value');
    }
}

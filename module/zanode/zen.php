<?php
declare(strict_types=1);
/**
 * The zen file of zanode module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Qiyu Xie <xieqiyu@easycorp.ltd>
 * @package     admin
 * @link        https://www.zentao.net
 */
class zanodeZen extends zanode
{
    /**
     * 操作执行节点。
     * Handle node.
     *
     * @param  int    $nodeID
     * @param  string $type boot|destroy|suspend|reboot|resume
     * @access protected
     * @return void
     */
    protected function handleNode(int $nodeID, string $type)
    {
        $node = $this->zanode->getNodeByID($nodeID);

        if(in_array($node->status, array('restoring', 'creating_img', 'creating_snap')))
        {
            return $this->sendError(sprintf($this->lang->zanode->busy, $this->lang->zanode->statusList[$node->status]), true);
        }

        $url    = 'http://' . $node->ip . ':' . $node->hzap . '/api/v1/kvm/' . $node->name . '/' . $type;
        $param  = array('vmUniqueName' => $node->name);
        $result = commonModel::http($url, $param, array(), array("Authorization:$node->tokenSN"), 'data', 'POST', 10);
        $result = json_decode($result, true);

        if(empty($result)) return $this->sendError($this->lang->zanode->notFoundAgent, true);

        if($result['code'] != 'success') return $this->sendError(zget($this->lang->zanode->apiError, $result['code'], $result['msg']), true);

        if($type != 'reboot')
        {
            $status = $type == 'suspend' ? 'suspend' : 'running';
            if($type == 'destroy') $status = 'shutoff';

            $this->dao->update(TABLE_ZAHOST)->set('status')->eq($status)->where('id')->eq($nodeID)->exec();
        }
        $this->loadModel('action')->create('zanode', $nodeID, ucfirst($type));
        return $this->sendSuccess(array('message' => $this->lang->zanode->actionSuccess, 'load' => true));
    }

    /**
     * 处理创建 zanode 请求数据。
     * Processing request data for creating zanode.
     *
     * @access protected
     * @return object
     */
    protected function prepareCreateExtras(): object
    {
        $data = form::data()
            ->setDefault('type', 'node')
            ->setDefault('createdDate', helper::now())
            ->setDefault('createdBy', $this->app->user->account)
            ->setDefault('status', 'running')
            ->setIF($this->post->hostType != 'physics', 'hostType', '')
            ->setIF($this->post->hostType == 'physics', 'parent', 0)
            ->setIF($this->post->hostType == 'physics', 'osName', $this->post->osNamePhysics)
            ->setIF($this->post->hostType == 'physics', 'secret', md5($this->post->name . time()))
            ->setIF($this->post->hostType == 'physics', 'status', 'offline')
            ->get();

        $checkResult = $this->zanode->checkFields4Create($data);
        if(!$checkResult) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        if($data->hostType != 'physics')
        {
            $result = $this->zanode->linkAgentService($data);
            if(!$result) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $data->mac = $result->data->mac;
            $data->vnc = (int)$result->data->vnc;
        }

        return $data;
    }

    /**
     * 处理创建快照请求数据。
     * Processing request data for creating snapshot.
     *
     * @param  int nodeID
     * @access protected
     * @return object
     */
    protected function prepareCreateSnapshotExtras(object $node): object
    {
        $data = form::data()->get();
        if(is_numeric($data->name)) return $this->sendError(array('name' => sprintf($this->lang->error->code, $this->lang->zanode->name)));

        $snapshot = new stdClass();
        $snapshot->host        = $node->id;
        $snapshot->name        = $data->name;
        $snapshot->desc        = $data->desc;
        $snapshot->status      = 'creating';
        $snapshot->osName      = $node->osName;
        $snapshot->memory      = 0;
        $snapshot->disk        = 0;
        $snapshot->fileSize    = 0;
        $snapshot->from        = 'snapshot';
        $snapshot->createdBy   = $this->app->user->account;
        $snapshot->createdDate = helper::now();

        return $snapshot;
    }

    /**
     * 获取导出镜像状态。
     * Get task status by zagent api.
     *
     * @param  object     $node
     * @param  int        $taskID
     * @param  string     $type
     * @param  string     $status
     * @access protected
     * @return false|array
     */
    protected function getTaskStatus(object $node, int $taskID = 0, string $type = '', string $status = ''): false|array
    {
        $agnetUrl = 'http://' . $node->ip . ':' . $node->hzap . '/api/v1/task/getStatus';
        $result   = json_decode(commonModel::http($agnetUrl, array(), array(CURLOPT_CUSTOMREQUEST => 'POST'), array("Authorization:$node->tokenSN"), 'json', 'POST', 10));
        if(empty($result) || $result->code != 'success') return false;

        $data = $result->data;
        if(empty($data)) return array();

        if($status && !$taskID && isset($data->$status)) return $data->$status;
        if(!$taskID) return $data;

        foreach($data as $status => $tasks)
        {
            if(empty($tasks)) continue;

            foreach($tasks as $task)
            {
                if(!empty($tasks['inprogress']) && $task->task != $tasks['inprogress'][0]->task && $task->status == 'created') $task->status = 'pending';
                if($type == $task->type && $taskID == $task->task) return $task;
            }
        }
        return $result;
    }
}

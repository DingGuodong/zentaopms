<?php
/**
 * The bug change point of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     entries
 * @version     1
 * @link        http://www.zentao.net
 **/

class storyActiveEntry extends entry
{
    /**
     * POST method.
     *
     * @param  int    $storyID
     * @access public
     * @return string
     */
    public function post($storyID)
    {
        $fields = 'assignedTo,status,comment';
        $this->batchSetPost($fields);

        $control = $this->loadController('story', 'activate');

        $control->activate($storyID);

        $data = $this->getData();
        if(!$data or !isset($data->status)) return $this->send400('error');
        if(isset($data->status) and $data->status == 'fail') return $this->sendError(zget($data, 'code', 400), $data->message);

        $story = $this->loadModel('story')->getByID($storyID);

        return $this->send(200, $story);
    }
}


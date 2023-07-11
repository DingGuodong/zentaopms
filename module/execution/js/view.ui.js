$(function()
{
    setTimeout(function()
    {
        blocks.forEach(block =>
        {
            if(block.domID)
            {
                block.content = {html: $(`#${block.domID}`).html()};
                $(`#${block.domID}`).remove();
                delete(block.domID);
            }
        });

        const $dashboard = $('#executionDashBoard').data('zui.Dashboard')
        $('#executionDashBoard .dashboard-block').attr('draggable', false);
        $dashboard.render({blocks: blocks});
    }, 10);
});

/**
 * 提示并删除执行。
 * Delete execution with tips.
 *
 * @param  int    executionID
 * @param  string executionName
 * @access public
 * @return void
 */
window.confirmDeleteExecution = function(executionID, confirmDeleteTip)
{
    zui.Modal.confirm({message: confirmDeleteTip, icon:'icon-info-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) =>
    {
        if(res) $.ajaxSubmit({url: $.createLink('execution', 'delete', 'executionID=' + executionID + '&comfirm=yes')});
    });
}

$(document).off('click','.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('taskIdList[]', id));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data: form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
});

/**
 * 计算表格信息的统计。
 * Set summary for table footer.
 *
 * @param  element element
 * @param  array   checkedIdList
 * @access public
 * @return object
 */
window.setStatistics = function(element, checkedIdList)
{
    const checkedTotal = checkedIdList.length;
    if(checkedTotal == 0) return {html: summary};

    let checkedEstimate = 0;
    let checkedCase     = 0;

    const dtable = zui.DTable.query($(this).target);
    checkedIdList.forEach((rowID) => {
        const task = dtable.$.getRowInfo(rowID);
        if(task)
        {
            checkedEstimate += task.data.estimate;
            if(cases[rowID]) checkedCase += 1;
        }
    })

    const rate = Math.round(checkedCase / checkedTotal * 10000) / 100 + '' + '%';
    return {
        html: checkedSummary.replace('%total%', checkedTotal)
            .replace('%estimate%', checkedEstimate)
            .replace('%rate%', rate)
    };
}

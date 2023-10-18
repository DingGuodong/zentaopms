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
}).off('click', '#actionBar .export').on('click', '#actionBar .export', function()
{
    const dtable = zui.DTable.query($('#table-execution-task'));
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    $.cookie.set('checkedItem', checkedList);
});

/**
 * 计算表格任务信息的统计。
 * Set task summary for table footer.
 *
 * @param  element element
 * @param  array   checkedIDList
 * @access public
 * @return object
 */
window.setStatistics = function(element, checkedIDList)
{
    let totalLeft     = 0;
    let totalEstimate = 0;
    let totalConsumed = 0;

    let waitCount  = 0;
    let doingCount = 0;
    let totalCount = 0;

    const rows  = element.layout.allRows;
    rows.forEach((row) => {
        if(checkedIDList.length == 0 || checkedIDList.includes(row.id))
        {
            const task = row.data;

            totalEstimate += Number(task.estimate);
            totalConsumed += Number(task.consumed);
            if(task.status != 'cancel' && task.status != 'closed') totalLeft += Number(task.left);

            if(task.status == 'wait')
            {
                waitCount ++;
            }
            else if(task.status == 'doing')
            {
                doingCount ++;
            }

            totalCount ++;
        }
    })

    const summary = checkedIDList.length > 0 ? checkedSummary : pageSummary;
    return {
        html: summary.replace('%total%', totalCount)
            .replace('%wait%', waitCount)
            .replace('%doing%', doingCount)
            .replace('%estimate%', totalEstimate.toFixed(1))
            .replace('%consumed%', totalConsumed.toFixed(1))
            .replace('%left%', totalLeft.toFixed(1))
    };
}

/**
 * 对部分列进行重定义。
 * Redefine the partial column.
 *
 * @param  array  result
 * @param  array  info
 * @access public
 * @return string|array
 */
window.renderCell = function(result, info)
{
    if(info.col.name == 'name' && result)
    {
        const task = info.row.data;
        let html = '';

        const module = this.options.modules[info.row.data.module];
        if(module) html += '<span class="label lighter rounded-full">' + module + '</span>'; // 添加模块标签

        if(task.team)
        {
            html += "<span class='label gray-pale rounded-xl'>" + multipleAB + "</span>";
        }
        if(task.parent)
        {
            html += "<span class='label gray-pale rounded-xl'>" + childrenAB + "</span>";
        }
        if(html) result.unshift({html});
    }
    if(info.col.name == 'deadline' && result[0])
    {
        const today     = zui.formatDate(zui.createDate(), 'yyyy-MM-dd');
        const yesterday = zui.formatDate(convertStringToDate(today) - 24 * 60 * 60 * 1000, 'yyyy-MM-dd');
        if(result[0] == today)
        {
            result[0] = {html: '<span class="label warning-pale rounded-full size-sm">' + todayLabel + '</span>'};
        }
        else if(result[0] == yesterday)
        {
            result[0] = {html: '<span class="label danger-pale rounded-full size-sm">' + yesterdayLabel + '</span>'};
        }
        else if(result[0] < yesterday)
        {
            result[0] = {html: '<span class="label danger-pale rounded-full size-sm">' + result[0] + '</span>'};
        }
    }
    return result;
}

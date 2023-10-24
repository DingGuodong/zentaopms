$(document).off('click', '#showTask').on('click', '#showTask', function()
{
    const show = $(this).is(':checked') ? 1 : 0;
    $.cookie.set('showTask', show, {expires:config.cookieLife, path:config.webRoot});

    loadCurrentPage();
});

$(document).off('click','.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('executionIDList[]', id.replace("pid", '')));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data:form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
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

const today = zui.formatDate(new Date(), 'yyyy-MM-dd');
window.onRenderCell = function(result, {col, row})
{
    if(col.name == 'nameCol')
    {
        const executionLink = $.createLink('execution', 'task', `executionID=${row.data.rawID}`);
        const executionType = typeList[row.data.type];
        let executionName   = '';

        if(typeof executionType != 'undefined') executionName += `<span class='label secondary-pale'>${executionType}</span> `;
        executionName += (!row.data.isParent) ? `<a href="${executionLink}" class="text-primary">${row.data.name}</a>` : row.data.name;
        executionName += (today > row.data.end) ? `<span class="label danger-pale ml-1">${delayed}</span>` : '';

        result[result.length] = {html: executionName};
        return result;
    }

    return result;
}

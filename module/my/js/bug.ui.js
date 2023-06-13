$(document).off('click', '.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('bugIdList[]', id));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data: form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
});

window.onRenderBugNameCell = function(result, info)
{
    if(info.col.name === 'title' && info.row.data.case)
    {
        result[result.length] = {html: '<a href=\'' + testcaseLink.replace('{case}', info.row.data.case).replace('{caseVersion}', info.row.data.caseVersion) + '\' title=\'' + info.row.data.case + '\'>' + testcaseTitle.replace('{case}', info.row.data.case) + '</a>'};
    }

    return result;
}

window.renderCell = function(result, {col, row})
{
    if(col.name === 'name')
    {
        if(row.data.postponed) result[result.length] = {html:'<span class="label size-sm circle danger-pale">' + row.data.delayInfo + '</span>', className:'flex items-end w-full', style:{flexDirection:"column"}};
        return result;
    }

    if(col.name === 'budget' && typeof(row.data.exceedBudget) != 'undefined')
    {
        let iconSign = ' <span class="icon icon-exclamation mr-2 text-danger"></span>';
        let menu     = '<menu class="dropdown-menu custom">';
        let dropMenu = menu;
        dropMenu    += '<div class="mb-1"><span class="text-gray">' + projectBudgetLang + ': </span><span class="font-bold">' + row.data.rawBudget + '</span></div>';
        dropMenu    += '<div class="mb-1"><span class="text-gray">' + remainingBudgetLang + ': </span><span class="font-bold">' + row.data.remainingBudget + '</span></div>';
        dropMenu    += '<div class="text-danger">' + exceededBudgetLang + ': <span class="font-bold">' + row.data.exceedBudget + '</span></div>';

        if(row.data.type == 'program')
        {
            if(row.data.parent == 0) iconSign = ' <span class="icon icon-exclamation-sign mr-2 text-danger"></span>';
            dropMenu  = menu;
            dropMenu += '<div class="mb-1"><span class="text-gray">' + programBudgetLang + ': </span><span class="font-bold">' + row.data.rawBudget + '</span></div>';
            dropMenu += '<div class="mb-1"><span class="text-gray">' + sumSubBudgetLang + ': </span><span class="font-bold">' + row.data.subBudget + '</span></div>';
            dropMenu += '<div class="text-danger">' + exceededBudgetLang + ': <span class="font-bold">' + row.data.exceedBudget + '</span></div>';
        }
        iconSign = '<span data-toggle="dropdown" data-trigger="hover" data-placement="right-start">' + iconSign + '</span>';
        result[0] = {html: '<div>' + row.data.budget + iconSign + dropMenu + '</div>', className:'flex items-end w-full items-end', style:{flexDirection:"column"}};
        return result;
    }

    if(col.name === 'invested')
    {
        result[0] = {html: '<div>' + row.data.invested + ' <small class="text-gray">' + langManDay + '</small></div>', className:'flex items-end w-full items-end', style:{flexDirection:"column"}};
        return result;
    }

    return result;
}

window.confirmDelete = function(projectID, module, projectName)
{
    var deleteURL = $.createLink(module, 'delete', "projectID=" + projectID);
    if(module == 'program') deleteURL = $.createLink('program', 'delete', "programID=" + projectID + '&confirm=yes');

    zui.Modal.confirm({message: confirmDeleteLang[module].replace('%s', projectName), icon:'icon-info-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) =>
    {
        if(res) $.ajaxSubmit({url: deleteURL, load: true});
    });
}

$(document).on('click', '.batch-btn', function()
{
    const $this  = $(this);
    const dtable = zui.DTable.query($this);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const postData = new FormData();
    checkedList.forEach((id) => postData.append('projectIdList[]', id));
    postAndLoadPage($(this).data('url'), postData, '', {app: 'project'});
})

/* Put the checked projects to cookie for exporting. */
$(document).on('click', '.has-checkbox', function()
{
    const $this  = $(this);
    const dtable = zui.DTable.query($this);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    $.cookie.set('checkedItem', checkedList);
})

$(document).off('click','.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('storyIdList[]', id));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data: form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
}).on('click', '#taskModal button[type="submit"]', function()
{
    const taskType = $('[name=type]').val();
    if(taskType.length == 0)
    {
        zui.Modal.alert(typeNotEmpty);
        return false;
    }

    const hourPoint = $('[name=hourPointValue]').val();
    if(typeof(hourPoint) === undefined) hourPoint = 0;

    if(hourPoint == 0)
    {
        zui.Modal.alert(hourPointNotEmpty);
        return false;
    }

    if(typeof(hourPoint) != 'undefined' && (isNaN(hourPoint) || hourPoint < 0))
    {
        zui.Modal.alert(hourPointNotError);
        return false;
    }

    const checkedIdList  = $('#storyIdList').val().split(',');
    let linkedTaskIdList = '';
    let unlinkTaskIdList = '';
    checkedIdList.forEach(function(storyID)
    {
        if(linkedTaskStories[storyID])
        {
            linkedTaskIdList += '[' + storyID +']';
        }
        else
        {
            unlinkTaskIdList += storyID + ',';
        }
    });

    if(linkedTaskIdList)
    {
        confirmStoryToTaskTip = confirmStoryToTask.replace('%s', linkedTaskIdList);

        if(confirm(confirmStoryToTaskTip))
        {
            $('#storyIdList').val(checkedIdList);
        }
        else
        {
            if(!unlinkTaskIdList) return false;

            $('#storyIdList').val(unlinkTaskIdList);
        }
    }

    zui.Modal.hide('#taskModal');

    const formData = new FormData($("#toTaskForm")[0]);
    postAndLoadPage($('#toTaskForm').attr('action'), formData);


    return false;
});

$(document).off('click', '#linkStoryByPlan button[type="submit"]').on('click', '#linkStoryByPlan button[type="submit"]', function()
{
    var planID = $('[name=plan]').val();
    if(planID)
    {
        $.ajaxSubmit({url: $.createLink('execution', 'importPlanStories', 'executionID=' + executionID + '&planID=' + planID)});
    }

    return false;
})

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

    $('#storyIdList').val(checkedIdList.join(','));

    let checkedEstimate = 0;
    let checkedCase     = 0;
    let SRTotal         = 0;
    let total           = 0;

    checkedIdList.forEach((rowID) => {
        const story  = element.getRowInfo(rowID);
        if(storyType == 'requirement' && story.data.type == 'story') SRTotal += 1;
        if(storyType == story.data.type) total += 1;
        if(story)
        {
            checkedEstimate += parseFloat(story.data.estimate);
            if(cases[rowID]) checkedCase += 1;
        }
    })

    const rate = Math.round(checkedCase / checkedTotal * 10000) / 100 + '' + '%';
    return {html: checkedSummary.replace('%total%', total).replace('%estimate%', checkedEstimate).replace('%rate%', rate).replace('%SRTotal%', SRTotal)};
}

window.renderStoryCell = function(result, info)
{
    const story = info.row.data;
    if(info.col.name == 'title' && result)
    {
        let html = '';

        if(story.parentName != undefined && story.parent > 0 && ($.cookie.get('tab') == 'execution' || ($.cookie.get('tab') == 'project' && hasProduct))) html += story.parentName + ' / ';
        if(typeof modulePairs[story.moduleID] != 'undefined') html += "<span class='label gray-pale rounded-xl clip'>" + modulePairs[story.moduleID] + "</span> ";
        if(story.parent > 0) html += "<span class='label gray-pale rounded-xl'>" + (storyType == 'requirement' ? 'SR' : childrenAB) + "</span>";
        if(html) result.unshift({html});
    }
    return result;
};

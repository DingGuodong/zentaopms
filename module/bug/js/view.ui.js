$(document).on('click', '#toStory', function()
{
    const message = $(this).data('confirm');
    const url     = $(this).data('url');
    zui.Modal.confirm({message, onResult: function(result)
    {
        if(result) loadPage(url);
    }});
});

$(document).on('click', '#toTaskButton', function()
{
    const projectID   = $('[name="taskProjects"]').val();
    const executionID = $('[name="execution"]').val() ? $('[name="execution"]').val() : 0;
    changeTaskProjects();

    if(projectID && executionID != 0)
    {
        const url = $.createLink('task', 'create', 'executionID=' + executionID + '&storyID=0&moduleID=0&taskID=0&todoID=0&extra=projectID=' + projectID + '&bugID=' + bugID);
        loadPage(url);
    }
    else if(projectID == 0)
    {
        zui.Modal.alert(errorNoProject);
    }
    else
    {
        zui.modal.alert(errorNoExecution);
    }

});

function changeTaskProjects(event)
{
    const projectID = event != undefined ?  $(event.target).val() : $('[name="project"]').val();
    const link      = $.createLink('product', 'ajaxGetExecutions', 'productID=' + productID + '&projectID=' + projectID +'&branch=' + branchID + '&number=&executionID=0&from=bugToTask');
    $.get(link, function(data)
    {
        let $executionPicker = $('[name="execution"]').zui('picker');
        if(data)
        {
            data = JSON.parse(data);
            $executionPicker.render({items: data});
        }
    });
}

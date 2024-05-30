window.setModuleAndPlanByBranch = function(e)
{
    const $branch  = $(e.target);
    const branchID = $branch.val();
    let $row       = $branch.closest('tr');

    var moduleLink = $.createLink('tree', 'ajaxGetOptionMenu', 'productID=' + productID + '&viewtype=story&branch=' + branchID + '&rootModuleID=0&returnType=html&fieldID=&extra=nodeleted');

    while($row.length)
    {
        const $modulePicker = $row.find('[name^=module]').zui('picker');
        const moduleID      = $row.find('[name^=module]').val();
        $.getJSON(moduleLink, function(data)
        {
            $modulePicker.render({items: data.items})
            $modulePicker.$.setValue(moduleID);
        });

        $row = $row.next('tr');
        if(!$row.find('td[data-name="module"][data-ditto="on"]').length) break;
    }

    var planLink = $.createLink('productPlan', 'ajaxGetProductPlans', 'productID=' + productID + '&branch=' + branchID);
    let $rows    = $branch.closest('tr');
    while($rows.length)
    {
        const $planPicker = $rows.find('[name^=plan]').zui('picker');
        const planID      = $rows.find('[name^=plan]').val();
        $.getJSON(planLink, function(data)
        {
            $planPicker.render({items: data})
            $planPicker.$.setValue(planID);
        });

        $rows = $rows.next('tr');
        if(!$rows.find('td[data-name="plan"][data-ditto="on"]').length) break;
    }
}

window.changeRegion = function(e)
{
    const $region  = $(e.target);
    const regionID = $region.val();
    const laneLink = $.createLink('kanban', 'ajaxGetLanes', 'regionID=' + regionID + '&type=story&field=lane');
    $.get(laneLink, function(lane)
    {
        $region.closest('tr').find('[name^=lane]').zui('picker').render((JSON.parse(lane)));
        $region.closest('tr').find('[name^=lane]').zui('picker').$.setValue(0);
    });

}

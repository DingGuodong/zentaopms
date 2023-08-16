window.renderRowData = function($row, index, row)
{
    /* If there are multiple branch products, show the branches. */
    if(isBranchProduct == 1)
    {
        let $branch = $row.find('[data-name="branch"] .picker').zui('picker');

        /* The product of current bug is multiple branch product, show branches in the select box. */
        if(products[row.product] != undefined && products[row.product].type != 'normal' && branchTagOption[row.product] != undefined)
        {
            $branch.render({items: branchTagOption[row.product], disabled: false});
        }
        /* The product of current bug isn't multiple branch product, disable the input box. */
        else
        {
            $branch.render({disabled: true});
        }
        $branch.$.changeState({value: ''});
    }

    /* Show the modules of current bug's product. */
    if(modules[row.product] != undefined && modules[row.product][row.branch] != undefined)
    {
        $row.find('[data-name="module"]').find('.picker-box').on('inited', function(e, info)
        {
            let bugModules = modules[row.product][row.branch];
            let $module    = info[0];
            $module.render({items: bugModules});
            $module.$.setValue(row.module);
        });
    }

    /* Show the bugs of current bug's product. */
    if(productBugList[row.product] != undefined && productBugList[row.product][row.branch] != undefined)
    {
        let duplicateBugs = productBugList[row.product][row.branch];
        $row.find('[data-name="duplicateBug"]').find('.picker-box').on('inited', function(e, info)
        {
            let $duplicateBug = info[0];
            $duplicateBug.render({items: duplicateBugs});
        });
    }

    /* Change assigner. */
    let $assignedTo = $row.find('.form-batch-input[data-name="assignedTo"]');
    if(row.status == 'closed')
    {
        /* If the status of the bug is closed, assigner of the bug is closed. */
        $assignedTo.replaceWith('<input class="form-control form-batch-input" name="assignedTo[' + row.id + ']" id="assignedTo_0" data-name="assignedTo" disabled="disabled" value="' + row.assignedTo.slice(0, 1).toUpperCase() + row.assignedTo.slice(1) + '">');
    }
    else if(tab == 'project' || tab == 'execution')
    {
        let assignedToList;
        if(row.execution != 0)
        {
            /* If bug has execution, assigner of the bug is all members of the execution. */
            assignedToList = executionMembers[row.execution];
        }
        else if(row.project != 0)
        {
            /* If bug has project, assigner of the bug is all members of the project. */
            assignedToList = projectMembers[row.project];
        }
        else if(productMembers.length > 0)
        {
            /* If bug has product and the product has members, assigner of the bug is all members of the product. */
            assignedToList = productMembers[row.product];
        }

        /* If the assignedToList is defined, replace assigner with assignedToList. */
        if(assignedToList != undefined)
        {
            $assignedTo.empty();
            $assignedTo.append('<option value=""></option>');
            $.each(assignedToList, function(assignedToID, assignedToName)
            {
                if(assignedToID != row.id) $assignedTo.append('<option value="' + assignedToID + '">' + assignedToName + '</option>');
            });
        }
    }
    $assignedTo.find('option[value="closed"]').remove();
}

function setDuplicate(event)
{
    const $target     = $(event.target);
    const $currentRow = $target.closest('tr');
    const resolution  = $target.val();
    if(resolution == 'duplicate')
    {
        $currentRow.find('.form-batch-input[data-name="duplicateBug"]').removeClass('hidden');
    }
    else
    {
        $currentRow.find('.form-batch-input[data-name="duplicateBug"]').addClass('hidden');
    }
}

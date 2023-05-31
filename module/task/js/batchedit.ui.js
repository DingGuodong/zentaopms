window.renderRowData = function($row, index, row)
{
    const executionID  = row.execution;
    let   members      = [];
    let   teamAccounts = executionTeams[executionID] != undefined ? executionTeams[executionID] : [];
    $.each(teamAccounts, function(index, teamAccount)
    {
        members[teamAccount] = users[teamAccount];
    });

    let taskMembers = [];
    if(teams[row.id] != undefined)
    {
        teamAccounts = teams[row.id];
        $.each(teamAccounts, function(index, teamAccount)
        {
            taskMembers[teamAccount.account] = users[teamAccount.account];
        });
    }
    else
    {
        if(row.status == 'closed') members['closed'] = 'Closed';
        taskMembers = members;
    }

    let $assignedTo = $row.find('.form-batch-input[data-name="assignedTo"]').empty();
    if(teams[row.id] != undefined && ((row.assignedTo != currentUser && row.mode == 'linear') || taskMembers[currentUser] == undefined))
    {
        $assignedTo.attr('disabled', 'disabled');
    }

    if(row.assignedTo && taskMembers[row.assignedTo] == undefined) taskMembers[row.assignedTo] = users[row.assignedTo];
    $assignedTo.append('<option value=""></option>');
    for(let account in taskMembers)
    {
        $assignedTo.append('<option value="' + account +'"' + (row.assignedTo == account ? 'selected' : '') + '>' + taskMembers[account] + '</option>');
    }

    if(teams[row.id] != undefined || row.parent < 0)
    {
        $row.find('.form-batch-input[data-name="estimate"]').attr('disabled', 'disabled');
        $row.find('.form-batch-input[data-name="consumed"]').attr('disabled', 'disabled');
        $row.find('.form-batch-input[data-name="left"]').attr('disabled', 'disabled');
    }
}

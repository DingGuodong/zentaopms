window.backup = function(e)
{
    if(backupError)
    {
        zui.Modal.alert(backupError);
        return;
    }
    var backupBtn = $('#actionBar .backup');
    backupBtn.toggleClass('loading');
    backupBtn.text(getSpaceLoading);
    $.get($.createLink('backup', 'ajaxGetDiskSpace'), function(data)
    {
        backupBtn.toggleClass('loading');
        backupBtn.text(startBackup);
        if(data)
        {
            if(data.needSpace > data.freeSpace)
            {
                zui.Modal.alert(alertTips.replace('NEED_SPACE', (data.needSpace / (1024 * 1024 * 1024)).toFixed(2)));
            }
            else
            {
                backupData();
            }
        }
    }, 'json');
}

window.getCellSpan = function(cell)
{
    if((cell.col.name == 'time' || cell.col.name == 'actions') && cell.row.data.rowspan)
    {
        return {rowSpan: cell.row.data.rowspan};
    }
}

function backupData()
{
    var timeID = null;
    $.ajax(
    {
        url: $('.backup').attr('data-link'),
        success: function (data)
        {
            clearInterval(timeID);
            $('#message').append(data);
            setTimeout(function(){return location.href = $.createLink('backup', 'index');}, 2000);
        },
        error: function(request, textstatus, error)
        {
            clearInterval(timeID);
            if(textstatus == 'timeout') $('#message').append("<p class='text-danger'>" + backupTimeout + '</p>');
            setTimeout(function(){return location.href = $.createLink('backup', 'index');}, 2000);
        }
    });

    $('#waitting .modal-body #backupType').html(backup);
    zui.Modal.open({id: 'waiting', size: 'sm', backdrop: false});
    $('#waitting').addClass('show');

    timeID = setInterval(function()
    {
        $.get($.createLink('backup', 'ajaxGetProgress'), function(data)
        {
            $('#message').html(data);
        });
    }, 1000);
}

window.getCellSpan = function(cell)
{
    if((cell.col.name == 'time' || cell.col.name == 'actions') && cell.row.data.rowspan)
    {
        return {rowSpan: cell.row.data.rowspan};
    }
};

window.restore = function(name)
{
    zui.Modal.confirm({message: confirmRestoreLang, icon:'icon-exclamation-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) =>
    {
        if(res)
        {
            $.ajaxSubmit({url: $.createLink('backup', 'restore', 'file=' + name)});

            zui.Modal.open({id: 'waiting', size: 'sm', backdrop: false});
            $('#waiting').addClass('show');
            $('#waiting .modal-body #backupType').html(restoreLang);
            $('#waiting .modal-body #message').empty();
        }
    });
};

$(document).off('click', '.rmPHPHeader').on('click', '.rmPHPHeader', function(data)
{
    if($(this).hasClass('disabled')) return false;

    zui.Modal.open({id: 'waiting', size: 'sm', backdrop: false});
    $('#waiting').addClass('show');
    $('#waiting .modal-body #backupType').html(rmPHPHeaderLang);
    $('#waiting .modal-body #message').empty();
});

let deleting = false;
$(document).off('click', '[data-confirm]').on('click', '[data-confirm]', function(){deleting = true;});
$(document).off('click', '.modal [z-key="cancel"]').on('click', '.modal [z-key="cancel"]', function(){deleting = false;});
$(document).off('click', '.modal [data-dismiss="modal"]').on('click', '.modal [data-dismiss="modal"]', function(){deleting = false;});
$(document).off('click', '.modal [z-key="confirm"]').on('click', '.modal [z-key="confirm"]', function()
{
    if(deleting) $('#main').addClass('loading');
});

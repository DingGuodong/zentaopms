const timeIndex = times.findIndex(key => `${key}` === `${time}`);

window.changeType = function(e)
{
    const type     = e.target.value;
    const $tr      = $(e.target).closest('tr');
    const index    = $tr.data('index')
    const $nameBox = $tr.find('[data-name="name"]');

    let param = 'userID=' + userID + '&id=' + (+index + 1);
    if(type == 'task') param += '&status=wait,doing';

    if(moduleList.indexOf(type) !== -1)
    {
        link = $.createLink(type, objectsMethod[type], param);
        $.get(link, function(data)
        {
            data = JSON.parse(data);
            data.name = 'name';
            $nameBox.html("<div class='picker-box' id='name'></div>");
            $nameBox.find('#name').picker(data);
        })
    }
    else
    {
        $nameBox.html($('#nameInputBox').html());
        $nameBox.find('input[name=name]').attr('name', `name[${index}]`).attr('id', `name_${index}`)
    }
}

window.changFuture = function()
{
    const isChecked = $('#futureDate[type="checkbox"]').prop('checked');
    $todoDate = $('#todoDate').zui('datePicker');

    $('#batchCreateTodoForm #date').val($todoDate.$.state.value);
    $('#batchCreateTodoForm #futureDate').val(isChecked ? 1 : 0);
    $todoDate.render({disabled: isChecked});
}

window.initTime = function(e)
{
    let   $this   = $(e.target);
    let   $tr     = $this.closest('tr');
    let   $picker = $this.zui('picker');
    let   value   = $picker.$.value;
    let   $end    = $tr.find('[data-name=end]');
    const options = $picker.options;
    const items   = options.items;
    const isBegin = options.name.indexOf('begin') === 0;

    const index = parseInt($tr.data('index'));
    if(isBegin)
    {
        let endValue = '';
        items.forEach(function(item, timeIndex)
        {
            if(item.value == value)
            {
                endIndex = timeIndex + 3;
                endValue = items.length <= endIndex ? value : items[endIndex].value;
                return;
            }
        });
        $end.zui('picker').$.setValue(endValue);
        value = endIndex;
    }
    console.log(value);

    $('#batchCreateTodoForm tbody tr').each(function()
    {
        const trIndex = parseInt($(this).data('index'));
        if(trIndex > index)
        {
            let endValue = '';
            items.forEach(function(item, timeIndex)
            {
                if(item.value == value)
                {
                    endIndex = timeIndex + 3;
                    endValue = items.length <= endIndex ? value : items[endIndex].value;
                    return;
                }
            });
            $(this).find('[data-name="beginAndEnd"] [data-name="begin"]').zui('picker').$.setValue(value);
            $(this).find('[data-name="beginAndEnd"] [data-name="end"]').zui('picker').$.setValue(endValue);
            value = endIndex;
        }
    });
}

window.togglePending = function(e)
{
    $(e.target).closest('.input-group').find('.time-input').each(function()
    {
        $(this).zui('picker').render({disabled: e.target.checked});
    });
}

var nameDefaultHtml = $('#nameInputBox').html();

/**
 * 切换周期类型。
 * Toggle cycle type.
 *
 * @return void
 */
function changeCycleType()
{
    var cycleType = $('#cycleType input[type=radio]:checked').val();
    toggleCycleConfig(cycleType);
    $('.type-day .input-group, .config-day .input-control').removeClass('has-error');
}

/**
 * 切换周期设置的展示。
 * Toggle cycle setting display.
 *
 * @param  string cycleType  Type of cycle.
 * @return void
 */
function toggleCycleConfig(cycleType)
{

    $('.cycle-type-detail:not(.type-' + cycleType + ')').addClass('hidden');
    $('.cycle-type-detail.type-' + cycleType).removeClass('hidden');
}

/**
 * 切换私人事务，用于切换指派给的禁用状态。
 * Toggle private transactions for switching the disabled state assigned to.
 *
 * @param  object switcher
 * @return void
 */
function togglePrivate(switcher)
{
    $('#assignedTo').prop('disabled', switcher.checked);
}

/**
 * 更改指派给时。
 * change assignedTo.
 *
 * @return void
 */
function changeAssignedTo()
{
    var assignedTo = $('#assignedTo').val();
    if(assignedTo !== userAccount)
    {
        $('#private').prop('disabled', true);
        $('#private').closest('.checkbox-primary').addClass('disabled');
    }
    else
    {
        $('#private').prop('disabled', false);
        $('#private').closest('.checkbox-primary').removeClass('disabled');
    }
}

/**
 * 切换日期待定复选框。
 * Toggle date pending checkbox.
 *
 * @param  object switcher
 * @return void
 */
function togglePending(e)
{
    if($(e.target).prop('checked'))
    {
        $("[name='date']").attr('disabled','disabled');
    }
    else
    {
        $("[name='date']").removeAttr('disabled');
    }
}

/**
 * 加载不同类型数据列表，从而更改待办名称控件。
 * Load different types of list to change the name control.
 *
 * @param  string type        Type of selected todo.
 * @param  string id          ID of selected todo.
 * @param  string defaultType Default type of selected todo.
 * @param  int    todoID      ID of the closed todo type.
 * @return void
 */
function loadList(type, id, todoDefaultType, todoID)
{
    if(id)
    {
        nameBoxClass = '.name-box' + id;
        nameBoxID    = '#nameBox' + id;
    }
    else
    {
        nameBoxClass = '.name-box';
        nameBoxID    = '#nameBox';
    }

    id = id ? id : '';
    var param = 'userID=' + userID + '&id=' + id;
    if(type == 'task') param += '&status=wait,doing';

    if(todoDefaultType && type == todoDefaultType && todoID != 0) param += '&objectID=' + todoID;

    if(moduleList.indexOf(type) !== -1)
    {
        link = $.createLink(type, objectsMethod[type], param);
        $.get(link, function(data, status)
        {
            if(data.length != 0)
            {
                if($(nameBoxClass).find('#nameInputBox').html(data).find('select').chosen) $(nameBoxClass).find('#nameInputBox').html(data).find('select').chosen();
                if(config.currentMethod == 'edit' || type == 'feedback') $(nameBoxClass).find('select').val(todoID);
                if($(nameBoxClass + ' select').val() == null) $(nameBoxClass + ' select').attr('data-placeholder', noOptions);
            }
            else
            {
                if($(nameBoxClass).find('#nameInputBox').html("<select id="+ type +" class='form-control'></select>").find('select').chosen) $(nameBoxClass).find('#nameInputBox').html("<select id="+ type +" class='form-control'></select>").find('select');
            }
        });
    }
    else
    {
        $(nameBoxClass).find('#nameInputBox').html(nameDefaultHtml);
    }

    if(nameBoxLabel) return;

    var formLabel = type == 'custom' ||(vision && vision == 'rnd') ?  nameBoxLabel.custom : nameBoxLabel.objectID;
    $('#nameBox .form-label').text(formLabel);
}

/**
 * 选择开始时间后，自动给出默认终止时间。
 * After selecting the start time, the default end time is automatically given.
 *
 * @return void
 */
function selectNext()
{
    if(!$('#begin ')[0] || !$('#end ')[0]) return;
    $('#end ')[0].selectedIndex = $('#begin ')[0].selectedIndex + 3;
    $('#end').trigger('chosen:updated');
}

/**
 * 切换起止时间的禁用状态。
 * Switch the disabled state of start and end time.
 *
 * @param  object switcher
 * @return void
 */
function switchDateFeature(e)
{
    if($(e.target).prop('checked'))
    {
        $("[name='begin'], [name='end']").attr('disabled','disabled').trigger('chosen:updated');
    }
    else
    {
        $("[name='begin'], [name='end']").removeAttr('disabled').trigger('chosen:updated');
    }
}

/**
 * 切换周期复选框的回调函数，用于页面交互展示。
 * Switch the cycle checkbox for page interactive display.
 *
 * @param  object switcher
 * @return void
 */
function showEvery(switcher)
{
    if(switcher.checked)
    {
        $('#spaceDay').removeAttr('disabled');
        $('.specify').addClass('hidden');
        $('.every').removeClass('hidden');
        $('#cycleYear').removeAttr('checked');
        $('#configSpecify, #configEvery').prop('checked', false);
    }
}

/**
 * 周期设置为天并为指定时，更改月份时获取天数。
 * When the cycle is set to days and specified, obtain the number of days when changing the month.
 *
 * @param  int specifiedMonth
 * @return void
 */
function setDays(e)
{
    var specifiedMonth = $(e.target).val()

    /* Get last day in specified month. */
    var date = new Date();
    date.setMonth(specifiedMonth);
    var month = date.getMonth() + 1;
    date.setMonth(month);
    date.setDate(0);
    var specifiedMonthLastDay = date.getDate();

    $('#specifiedDay').empty('');
    for(var i = 1; i <= specifiedMonthLastDay; i++)
    {
        html = "<option value='" + i + "' title='" + i + "' data-keys='" + i + "'>" + i + "</option>";

        $('#specifiedDay').append(html);
    }
}

/**
 * 更改日期。
 * Change date.
 *
 * @param  object dateInput
 * @return void
 */
function changeDate(dateInput)
{
    $('#switchDate').prop('checked', !$(dateInput).val());
}

/**
 * 验证间隔天数是否为空。
 * Verify if the sapceDay value is empty.
 *
 * @param  object spaceDay
 * @return void
 */
function verifySpaceDay(spaceDay)
{
    if(!$(spaceDay).val()) $(spaceDay).closest('.input-control').addClass('has-error');
}

/**
 * 验证周期类型为天的日期是否为空。
 * Verify if the date with a cycle type of days is empty.
 *
 * @param  object dateInput
 * @return void
 */
function verifyCycleDate(dateInput)
{
    if(!$(dateInput).val()) $(dateInput).closest('.input-group').addClass('has-error');
}

/**
 * 验证结束时间是否正确。
 * Verify if the end time is correct.
 *
 * @param  object time
 * @return void
 */
function verifyEndTime(time)
{
    if($(time).val() < $('#begin').val()) $(time).addClass('has-error');
}

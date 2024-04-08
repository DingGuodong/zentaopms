window.onRenderRow = function(row, rowIdx, data)
{
    if(row.children('[data-name=milestone]').find('input[type=radio]:checked').length == 0) row.children('[data-name=milestone]').find('input[type=radio]').eq(1).prop('checked', true);
    row.children('[data-name=type]').find('[name^=type]').picker({disabled: true});

    if(project.model == 'ipd')
    {
        var $attribute = data.attribute;
        var $point     = row.find('[data-name="point"]');

        $point.find('.picker-box').on('inited', function(e, info)
        {
            let $picker      = info[0];
            let options      = $picker.options;
            let items        = [{text: '', value: ''}];
            for(let point in ipdStagePoint[$attribute])
            {
                let $value = ipdStagePoint[$attribute][point];
                items.push({text: $value, value: $value}); 
            }
            options.items = items;

            $picker.render(options);
        });

        $('thead [data-name="ACTIONS"]').css('display', 'none');
        row.find('[data-name="ACTIONS"]').css('display', 'none');
        row.find('[data-name="attribute"]').find('.picker-box').on('inited', function(e, info)
        {
            let $attributePicker = info[0];
            $attributePicker.render({disabled: true});
        });

        if(data.hasOwnProperty('status') && data.status != 'wait')
        {
            row.find('[data-name=enabled] input').attr('disabled', 'disabled');
            row.find('[data-name=enabled]').attr('title', cropStageTip);
        }

        if(data.enabled == 'off')
        {
            /* 需要等该行所有input元素加载完再执行changeEnabled方法 */
            window.waitDom("tr[data-index='" + rowIdx + "'] [data-name='PM'] input", function(){changeEnabled(row.find('[data-name=enabled] [name^=enabled]'));});
        }
    }

    if(data != undefined)
    {
        if(data.hasOwnProperty('type'))
        {
            row.find('[data-name="type"]').find('.picker-box').on('inited', function(e, info)
            {
                let $type = info[0];
                $type.render({disabled: true});
            });
        }

        if(data.hasOwnProperty('id')) row.find('[data-name="ACTIONS"]').find('[data-type="delete"]').remove();
    }

    if(!data || !data.planIDList) return;

    row.children('.form-batch-row-actions').children('[data-type=delete]').addClass('hidden');
    row.children('[data-name=type]').children('select').attr('disabled', 'disabled');

    /* Use the flag variable 'setMilestone' to toggle the enable/disable status of the milestone field. */
    if(!data.setMilestone)
    {
        let name  = '';
        let value = '';
        const checkBoxList = row.children('[data-name=milestone]').find('input');
        Object.values(checkBoxList).forEach(function(ele){
            if(typeof ele !== 'object') return;

            name = ele.name;
            if(ele.checked)
            {
                value = ele.value;
            }
        });

        checkBoxList.attr('disabled', 'disabled');

        /* Append hidden input element for passing value. */
        let inputEle = document.createElement('input');
        inputEle.setAttribute('name',  name);
        inputEle.setAttribute('value', value);
        inputEle.setAttribute('type',  'hidden');
        row.children('[data-name=milestone]').append(inputEle);
    }
};

window.onChangeExecutionType = function(event)
{
    loadPage($.createLink('programplan', 'create', `projectID=${projectID}&productID=${productID}&planID=${planID}&type=` + $(event.target).val()));
};

/**
 * Add row errors.
 *
 * @param  array  $rowErrors
 * @access public
 * @return void
 */
window.addRowErrors = function(rowErrors)
{
    var errorFieldID, errorTip, errorHTML;
    $('.text-danger.help-text').remove();
    $('#dataform input').removeClass('has-error');
    var index = 0;
    var alterError = '';
    $('input[name^=name]').each(function()
    {
        if($(this).val() == '') return true;

        if(typeof rowErrors[index] == 'object')
        {
            for(var errorField in rowErrors[index])
            {
                $errorTD  = $(this).closest('tr').find('input[name^=' + errorField + ']').closest('td');
                errorTip  = rowErrors[index][errorField];
                errorHTML = '<div id="help' + errorField + index + '" class="text-danger help-text">' + errorTip + '</div>';
                $errorTD.append(errorHTML);
                $errorTD.find('input').addClass('has-error');
            }
        }
        if(typeof rowErrors['percent'] == 'string')
        {
            errorFieldID = $(this).closest('tr').find('input[name^=percent]').attr('id');
            errorHTML    = '<div id="help' + errorFieldID + '" class="text-danger help-text">' + rowErrors['percent'] + '</div>';
            $('#' + errorFieldID).closest('td').append(errorHTML);
            $('#' + errorFieldID).closest('td').find('input').addClass('has-error');
        }

        index ++;
    });
};

window.waitDom('td[data-name=milestone]', function()
{
    $('td[data-name=milestone]').each(function()
    {
        if($(this).find('input[type=radio]:checked').length == 0) $(this).find('input[type=radio]').eq(1).prop('checked', true);
    })
})

window.changeEnabled = function(obj)
{
    const $target    = $(obj);
    const $row       = $target.closest('tr');
    const tdItems    = $row.find('td');
    const stageID    = $row.find('input[name^=id]').val();
    const stageAttr  = $row.find('input[name^=attribute]').val();
    const defaultVal = {
            'name' : stageID ? plans[stageID].name  : attributeList[stageAttr],
            'begin': stageID ? plans[stageID].begin : project.begin,
            'end'  : stageID ? plans[stageID].end   : project.end
        }

    if($target.prop('checked'))
    {
        for(let item = 0; item < tdItems.length; item++)
        {
            if($(tdItems[item]).data('name') == 'attribute') continue;

            if($(tdItems[item]).find('[data-zui-datepicker]').length)
            {
                $(tdItems[item]).find("input.hidden").last().remove();

                $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').render({disabled: false});
            }
            else if($(tdItems[item]).find('[data-zui-picker]').length)
            {
                $(tdItems[item]).find("input.hidden").last().remove();

                $(tdItems[item]).find('[data-zui-picker]').zui('picker').render({disabled: false});
            }
            else if($(tdItems[item]).find('.radio-primary').length)
            {
                $(tdItems[item]).find("input.hidden").last().remove();

                $(tdItems[item]).find('.radio-primary').parent().removeAttr('disabled');
            }
            else if($(tdItems[item]).find('input[type=text]').length)
            {
                $(tdItems[item]).find('input[type=text]').removeAttr('readonly');
            }
            else if($(tdItems[item]).data('name') == 'enabled')
            {
                $(tdItems[item]).find("input.hidden").last().remove();
            }
        }
    }
    else
    {
        let itemValue = '';
        let itemName  = '';
        for(let item = 0; item < tdItems.length; item++)
        {
            if($(tdItems[item]).data('name') == 'attribute') continue;

            if($(tdItems[item]).find('[data-zui-datepicker]').length)
            {
                if($(tdItems[item]).find('input[name^=begin]').length) $(tdItems[item]).find('input[name^=begin]').zui('datePicker').$.setValue(defaultVal.begin);
                if($(tdItems[item]).find('input[name^=end]').length)   $(tdItems[item]).find('input[name^=end]').zui('datePicker').$.setValue(defaultVal.end);

                $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').render();

                itemValue = $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').$.value;
                itemName  = $(tdItems[item]).find('input.pick-value').attr('name');
                $(tdItems[item]).append("<input name='" + itemName + "' value='" + itemValue + "' class='hidden'/>");

                $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').render({disabled: true});
            }
            else if($(tdItems[item]).find('[data-zui-picker]').length)
            {
                itemValue = $(tdItems[item]).find('input, select').zui('picker').$.value;
                itemName  = $(tdItems[item]).find('input, select').attr('name');
                $(tdItems[item]).append("<input name='" + itemName + "' value='" + itemValue + "' class='hidden'/>");

                $(tdItems[item]).find('[data-zui-picker]').zui('picker').render({disabled: true});
            }
            else if($(tdItems[item]).find('.radio-primary').length)
            {
                itemValue = $(tdItems[item]).find('input[type=radio]:checked').val();
                itemName  = $(tdItems[item]).find('input[type=radio]:checked').attr('name');
                $(tdItems[item]).append("<input name='" + itemName + "' value='" + itemValue + "' class='hidden'/>");

                $(tdItems[item]).find('.radio-primary').parent().attr('disabled', 'disabled');
            }
            else if($(tdItems[item]).find('input[type=text]').length)
            {
                if($(tdItems[item]).find('input[name^=name]').length) $(tdItems[item]).find('input[name^=name]').val(defaultVal.name);

                $(tdItems[item]).find('input[type=text]').attr('readonly', 'readonly');
            }
            else if($(tdItems[item]).data('name') == 'enabled')
            {
                itemName = $(tdItems[item]).find('input').attr('name');
                $(tdItems[item]).append("<input name='" + itemName + "' value='off' class='hidden'/>")
            }
        }
    }
}

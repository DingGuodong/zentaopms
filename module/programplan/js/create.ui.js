window.onRenderRow = function(row, rowIdx, data)
{
    if(row.children('[data-name=milestone]').find('input[type=radio]:checked').length == 0) row.children('[data-name=milestone]').find('input[type=radio]').eq(1).prop('checked', true);
    row.children('[data-name=type]').find('[name^=type]').picker({disabled: true});

    if(project.model == 'ipd')
    {
        $('thead [data-name="ACTIONS"]').css('display', 'none');
        row.find('[data-name="ACTIONS"]').css('display', 'none');
        row.find('[data-name="attribute"]').find('.picker-box').on('inited', function(e, info)
        {
            let $attributePicker = info[0];
            $attributePicker.render({disabled: true});
        });
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

window.changeEnabled = function()
{
    const $target = $(this);
    const tdItems = $target.closest('tr').find('td');

    if($(this).prop('checked'))
    {
        for(let item = 0; item < tdItems.length; item++)
        {
            if($(tdItems[item]).data('name') == 'enabled') continue;
            if($(tdItems[item]).data('name') == 'attribute') continue;

            if($(tdItems[item]).find('[data-zui-datepicker]').length)
            {
                $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').render({disabled: false});
            }
            else if($(tdItems[item]).find('[data-zui-picker]').length)
            {
                $(tdItems[item]).find('[data-zui-picker]').zui('picker').render({disabled: false});
            }
            else if($(tdItems[item]).find('.radio-primary').length)
            {
                $(tdItems[item]).find('.radio-primary').parent().removeAttr('disabled');
            }
            else if($(tdItems[item]).find('input[type=text]').length)
            {
                $(tdItems[item]).find('input[type=text]').removeAttr('readonly');
            }
        }
    }
    else
    {
        for(let item = 0; item < tdItems.length; item++)
        {
            if($(tdItems[item]).data('name') == 'enabled') continue;
            if($(tdItems[item]).data('name') == 'attribute') continue;

            if($(tdItems[item]).find('[data-zui-datepicker]').length)
            {
                $(tdItems[item]).find('[data-zui-datepicker]').zui('datePicker').render({disabled: true});
            }
            else if($(tdItems[item]).find('[data-zui-picker]').length)
            {
                $(tdItems[item]).find('[data-zui-picker]').zui('picker').render({disabled: true});
            }
            else if($(tdItems[item]).find('.radio-primary').length)
            {
                $(tdItems[item]).find('.radio-primary').parent().attr('disabled', 'disabled');
            }
            else if($(tdItems[item]).find('input[type=text]').length)
            {
                $(tdItems[item]).find('input[type=text]').attr('readonly', 'readonly');
            }
        }
    }

}

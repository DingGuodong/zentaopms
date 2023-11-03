window.clickAddRows = function()
{
    const rowIndex =  $(this).closest('.form-row').data('row');
    let formRow = $(this).closest('.form-row').prop('outerHTML');
    formRow = formRow.replaceAll('name[' + rowIndex + ']', 'name[' + index + ']').replaceAll('WIPCount[' + rowIndex + ']', 'WIPCount[' + index + ']').replaceAll('noLimit[' + rowIndex + ']', 'noLimit[' + index + ']');
    $(this).closest('.form-row').after(formRow);
    index++;
    if($('.form-row').length > 2) $('.removeRows').removeClass('opacity-0').removeAttr('disabled');
}
window.clickRemoveRows = function(event)
{
    $(this).closest('.form-row').remove();
    if($('.form-row').length <= 2) $('.removeRows').addClass('opacity-0').attr('disabled', true);
}

window.changeColumnLimit = function()
{
    const noLimit = $(this).prop('checked');
    if(noLimit)
    {
        $(this).closest('.form-row').find('[name^=WIPCount]').val('').attr('disabled', true);
    }
    else
    {
        $(this).closest('.form-row').find('[name^=WIPCount]').removeAttr('disabled');
    }
}

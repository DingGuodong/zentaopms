function searchWords()
{
    const $target = $(event.target).closest('button');
    const $form = $target.closest('#mainContent').find('form');
    const words = $form.find('input[name=words]').val();
    if(words == '')
    {
        zui.Modal.alert(inputWords);
        return false;
    }

    const form = new FormData();
    form.append('words', words);

    const types = $form.find('select[name^=type]').val();
    types.forEach(type => form.append('type[]', type));

    postAndLoadPage($.createLink('search', 'index'), form);
}

function clearWords()
{
    $('#clearWords').addClass('hidden').closest('.input-group').find('input[name=words]').val('');
}

function toggleClearWords()
{
    $(event.target).closest('.input-group').find('#clearWords').toggleClass('hidden', $(event.target).val() == '');
}

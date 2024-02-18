function loadLibModules()
{
    const libID = $('#lib').val();
    const link = $.createLink('tree', 'ajaxGetOptionMenu', 'libID=' + libID + '&viewtype=caselib&branch=0&rootModuleID=0&returnType=items&fieldID=');

    $.get(link, function(data)
    {
        if(data)
        {
            let $libPicker = $('[name=lib]').zui('picker');
            data = JSON.parse(data);
            $libPicker.render({items: data});
            $libPicker.$.changeState({value: ''});
        }
    });
}

function loadProductRelated(event)
{
    const productID = $(event.target).val();

    loadProductBranches(productID);
    loadProductModules(productID);
    loadScenes(productID);
    loadProductStories(productID);
}

function loadBranchRelated()
{
    const productID = $('[name=product]').val();

    loadProductModules(productID);
    loadScenes(productID);
    loadProductStories(productID);
}

function loadModuleRelated()
{
    const productID = $('[name=product]').val();
    loadScenes(productID);
    if($('#story').length) loadProductStories(productID);
}

function checkScript()
{
    $('.autoScript').toggleClass('hidden', !$('#auto').prop('checked'));
}

window.readScriptContent = function(object)
{
    $uploadBtnLabel = $('[name=scriptFile]').siblings().first();
    $uploadBtnLabel.toggle($('[name=scriptFile]').siblings().first().parents('td').find('.file-list').length < 1);
    $uploadBtnLabel.hide();

    var reader = new FileReader();
    reader.readAsText(object.file, 'UTF-8');
    reader.onload = function(evt){$('#script').val(evt.target.result);}
}

window.showUploadScriptBtn = function()
{
    $('[name=scriptFile]').siblings().first().show();
    $('#script').val('');
}

$(document).off('click', '.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();
    checkedList.forEach((id) => form.append('caseIdList[]', id));

    if($(this).hasClass('ajax-btn'))
    {
        $.ajaxSubmit({url, data:form});
    }
    else
    {
        postAndLoadPage(url, form);
    }
});

/**
 * Set stories.
 *
 * @param  int     productID
 * @param  int     moduleID
 * @param  int     num
 * @access public
 * @return void
 */
function loadStories(productID, moduleID, num, $currentRow = null)
{
    var branchIDName = (config.currentMethod == 'batchcreate' || config.currentMethod == 'showimport') ? '#branch' : '#branches';
    var branchID     = config.currentMethod == 'batchcreate' ? $(branchIDName + '_' + num).val() : $(branchIDName + num).val();
    var storyLink    = $.createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&branch=' + branchID + '&moduleID=' + moduleID + '&storyID=0&onlyOption=false&status=noclosed&limit=0&type=full&hasParent=1&executionID=0&number=' + num);
    $.getJSON(storyLink, function(stories)
    {
        if(config.currentMethod == 'batchcreate')
        {
            if(!stories) return;

            let $row = $currentRow;
            while($row.length)
            {
                const $story = $row.find('.form-batch-input[data-name="story"]').empty();

                $.each(stories, function(index, story)
                {
                    $story.append('<option value="' + story.value + '">' + story.text + '</option>');
                });

                $row = $row.next('tr');

                if(!$row.find('td[data-name="story"][data-ditto="on"]').length || !$row.find('td[data-name="branch"][data-ditto="on"]').length) break;
            }
        }
        else
        {
            if(!stories) stories = '<select id="story' + num + '" name="story[' + num + ']" class="form-control"></select>';
            $('#story' + num).replaceWith(stories);
            $('#story' + num + "_chosen").remove();
            $('#story' + num).next('.picker').remove();
            $('#story' + num).attr('name', 'story[' + num + ']');
            $('#story' + num).picker();
        }
    });
}

/**
 * Set modules.
 *
 * @param  int     $branchID
 * @param  int     $productID
 * @param  int     $num
 * @access public
 * @return void
 */
function setModules(event)
{
    const $target     = $(event.target);
    const $currentRow = $target.closest('tr');
    const branchID    = $target.val();
    const moduleID    = $currentRow.find('.form-batch-input[data-name="module"]').val();

    $.getJSON($.createLink('tree', 'ajaxGetModules', 'productID=' + productID + '&viewType=case&branch=' + branchID + '&number=0&currentModuleID=' + moduleID), function(data)
    {
        if(!data || !data.modules) return;

        let $row = $currentRow;
        while($row.length)
        {
            const $module = $row.find('.form-batch-input[data-name="module"]').empty();

            $.each(data.modules, function(index, module)
            {
                $module.append('<option value="' + module.value + '"' + (module.value == data.currentModuleID ? 'selected' : '')  + '>' + module.text + '</option>');
            });

            $row = $row.next('tr');

            if(!$row.find('td[data-name="module"][data-ditto="on"]').length || !$row.find('td[data-name="branch"][data-ditto="on"]').length) break;
        }
    });

    loadStories(productID, moduleID, 0, $currentRow);
}

function loadProductStories(productID)
{
    let branch   = $('[name=branch]').val();
    let moduleID = $('[name=module]').val();
    let storyID  = $('[name=story]').val();

    if(typeof(branch)   == 'undefined') branch   = 0;
    if(typeof(moduleID) == 'undefined') moduleID = 0;
    if(typeof(storyID)  == 'undefined') storyID  = 0;

    const link = $.createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&branch=' + branch + '&moduleID=' + moduleID + '&storyID=' + storyID + '&onlyOption=false&status=noclosed&limit=0&type=full&hasParent=1&executionID=' + executionID);
    $.get(link, function(data)
    {
        if(data)
        {
            let $storyPicker = $('[name=story]').zui('picker');
            data = JSON.parse(data);
            $storyPicker.render({items: data});
            $storyPicker.$.changeState({value: ''});
        }
    })
}

function loadProductBranches(productID)
{
    var param     = config.currentMethod == 'create' ? 'active' : 'all';
    var oldBranch = config.currentMethod == 'edit' ? caseBranch : 0;
    var param     = 'productID=' + productID + '&oldBranch=' + oldBranch + '&param=' + param;
    if(typeof(tab) != 'undefined' && (tab == 'execution' || tab == 'project')) param += '&projectID=' + objectID;

    $.get($.createLink('branch', 'ajaxGetBranches', param), function(data)
    {
        if(data)
        {
            $('#branch').show();

            let $branchPicker = $('[name=branch]').zui('picker');
            data = JSON.parse(data);
            $branchPicker.render({items: data});
            $branchPicker.$.changeState({value: ''});
        }
        else
        {
            $('#branch').hide();
        }
    })
}

function loadProductModules(productID)
{
    let branch = $('[name=branch]').val();
    if(typeof(branch) == 'undefined') branch = 0;

    const currentModuleID = config.currentMethod == 'edit' ? $('[name=module]').val() : 0;
    const getModuleLink   = $.createLink('testcase', 'ajaxGetOptionMenu', 'productID=' + productID + '&viewtype=case&branch=' + branch + '&rootModuleID=0&returnType=html&fieldID=&needManage=true&extra=&currentModuleID=' + currentModuleID);

    $.get(getModuleLink, function(data)
    {
        if(data)
        {
            let $modulePicker = $('[name=module]').zui('picker');
            data = JSON.parse(data);
            $modulePicker.render({items: data});
            $modulePicker.$.changeState({value: ''});

            $('#module').next('.input-group-addon').toggleClass('hidden', data.length > 1);
        }
    })
}

function loadProductStories(productID)
{
    let branch   = $('[name=branch]').val();
    let moduleID = $('[name=module]').val();
    let storyID  = $('[name=story]').val();

    if(typeof(branch)   == 'undefined') branch   = 0;
    if(typeof(moduleID) == 'undefined') moduleID = 0;
    if(typeof(storyID)  == 'undefined') storyID  = 0;

    const link = $.createLink('story', 'ajaxGetProductStories', 'productID=' + productID + '&branch=' + branch + '&moduleID=' + moduleID + '&storyID=' + storyID + '&onlyOption=false&status=noclosed&limit=0&type=full&hasParent=1&executionID=' + executionID);
    $.get(link, function(data)
    {
        if(data)
        {
            let $storyPicker = $('[name=story]').zui('picker');
            data = JSON.parse(data);
            $storyPicker.render({items: data});
            $storyPicker.$.changeState({value: ''});
        }
    })
}

function loadScenes(productID, sceneName = 'scene')
{
    let branch   = $('[name=branch]').val();
    let moduleID = $('[name=module]').val();
    if(typeof(branch) == 'undefined')   branch   = 0;
    if(typeof(moduleID) == 'undefined') moduleID = 0;

    const link = $.createLink('testcase', 'ajaxGetModuleScenes', 'productID=' + productID + '&branch=' + branch + '&moduleID=' + moduleID + '&stype=2&storyID=0&onlyOption=false&status=noclosed&limit=50&type=full&hasParent=1');
    $.get(link, function(data)
    {
        let $scenePicker = $('[name=' + sceneName + ']').zui('picker');

        data = JSON.parse(data);
        $scenePicker.render({items: data});
        $scenePicker.$.changeState({value: ''});
    });
}

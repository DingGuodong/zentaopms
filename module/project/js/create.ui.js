$(function()
{
    setWhite();
});

window.addProduct = function(e)
{
    if($('.project-type-' + type).hasClass('disabled')) return;
    if($(e.target).prop('checked'))
    {
        $('.productBox').addClass('hidden');
        $('#addProductBox').removeClass('hidden');
        $("[name^='newProduct']").prop('checked', true);
    }
    else
    {
        $('.productBox').removeClass('hidden');
        $('#addProductBox').addClass('hidden');
        $("[name^='newProduct']").prop('checked', false);
    }
}

window.productChange = function(e)
{
    loadBranches(e.target);

    let current    = $(e.target).val();
    let last       = $(e.target).attr('last');
    let lastBranch = $(e.target).attr('data-lastBranch');

    $(e.target).attr('data-last', current);

    let $branch = $(e.target).closest('.has-branch').find("[name^='branch']");
    if($branch.length)
    {
        let branchID = $branch.val();
        $(e.target).attr('data-lastBranch', branchID);
    }
    else
    {
        $(e.target).removeAttr('data-lastBranch');
    }

    let chosenProducts = 0;
    $(".productsBox select[name^='products']").each(function()
    {
        if($(e.target).val() > 0) chosenProducts ++;
    });

    if(chosenProducts > 1)  $('.stageBy').removeClass('hide');
    if(chosenProducts <= 1) $('.stageBy').addClass('hide');
}

window.branchChange = function(e)
{
    let current = $(e.target).val();
    let last    = $(e.target).attr('data-last');
    $(e.target).attr('data-last', current);

    let $product = $(e.target).closest('.form-row').find("[name^='products']");
    $product.attr('data-lastBranch', current);

    loadPlans($product, $(e.target));
}

$(document).on('click', '#copyProjects button', function()
{
    const copyProjectID = $(this).hasClass('success-outline') ? 0 : $(this).data('id');
    setCopyProject(copyProjectID);
    zui.Modal.hide();
});

/**
 * Set copy project.
 *
 * @param  int $copyProjectID
 * @access public
 * @return void
 */
function setCopyProject(copyProjectID)
{
    const programID = $('#parent').val();
    loadPage($.createLink('project', 'create', 'model=' + model + '&programID=' + programID + '&copyProjectID=' + copyProjectID));
}

/**
 * Fuzzy search projects by project name.
 *
 * @access public
 * @return void
 */
$(document).on('keyup', '#projectName', function()
{
    var name = $(this).val();
    name = name.replace(/\s+/g, '');
    $('#copyProjects .project-block').hide();

    if(!name) $('#copyProjects .project-block').show();
    $('#copyProjects .project-block').each(function()
    {
        if($(this).text().includes(name) || $(this).data('pinyin').includes(name)) $(this).show();
    });
});

$(function()
{
    $('#mainNavbar .nav .nav-item').find("[href$='" + storyType + "']").addClass('active');
    if($.apps.currentCode == 'qa' && $('#mainNavbar .nav .nav-item').find("[href$='" + storyType + "']").length == 0)  $('#navbar  .nav .nav-item').find("[data-id='testcase']").addClass('active');
});

window.renderRowData = function($row, index, story)
{
    $row.find('[data-name="closedBy"]').find('.picker-box').on('inited', function(e, info)
    {
        let $picker = info[0];
        let options = $picker.options;
        options.disabled = story.rawStatus != 'closed';
        $picker.render(options);
    });

    $row.addClass('story' + story.id);

    var $closedReasonTD = $row.find('[data-name="closedReason"]');
    $closedReasonTD.find('.picker-box').on('inited', function(e, info)
    {
        $closedReasonTD.find('.picker-box').wrap("<div class='input-group'></div>");
        let $picker = info[0];
        let options = $picker.options;
        options.disabled = story.rawStatus != 'closed';
        if(story.rawStatus == 'closed') options.onChange = function(value){setDuplicateAndChild(value, story.id)};

        $picker.render(options);

        if(story.rawStatus == 'closed')
        {
            var productStories = typeof(productStoryList[story.product]) == 'undefined' ? [] : productStoryList[story.product];
            var productStories = typeof(productStories[story.branch]) == 'undefined' ? [] : productStories[story.branch];

            let appendStoryHtml = "<span id='duplicateStoryBox" + story.id + "' " + (story.closedReason != 'duplicate' ? "class='hidden'" : '') + ">";
            appendStoryHtml    += "<div class='form-control picker-box' data-name='duplicateStory' style='padding:0'></div></span>";

            $closedReasonTD.find('.input-group').append(appendStoryHtml);

            items = [];
            for(let storyID in productStories) items.push({text: productStories[storyID], value: storyID});
            $closedReasonTD.find('.picker-box[data-name=duplicateStory]').picker({items: items, name: 'duplicateStory[' + story.id + ']'});
        }
    });

    $row.find('[data-name="draft"]').find('.picker-box').on('inited', function(e, info)
    {
        let $picker = info[0];
        let options = $picker.options;
        options.disabled = story.rawStatus == 'draft';
        $picker.render(options);
    });

    var $title    = $row.find('.form-batch-input[data-name="title"]');
    var $module   = $row.find('.form-batch-control[data-name="module"]');
    var $plan     = $row.find('.form-batch-control[data-name="plan"]');
    var $branch   = $row.find('.form-batch-control[data-name="branch"]');
    var $stage    = $row.find('.form-batch-control[data-name="stage"]');
    var $estimate = $row.find('.form-batch-control[data-name="estimate"]');

    if($estimate.length > 0 && story.isParent == '1') $estimate.find('input.form-control').attr('readonly', 'readonly');

    if($stage.length > 0)
    {
        $stage.find('.picker-box').on('inited', function(e, info)
        {
            let $picker = info[0];
            let options = $picker.options;
            let items   = options.items;

            if(story.type == 'story' && story.isParent == '0')
            {
                /* 叶子需求删除父需求的阶段。*/
                items.splice(0, 1);
                items.splice(1, 1);
                items.splice(11, 1);
                options.items = items;
            }
            else if(story.type != 'story' || story.isParent == '1')
            {
                options.disabled = true;
            }

            $picker.render(options);
        })
    }

    $title.attr('disabled', 'disabled').attr('title', story.title).after("<input type='hidden' name='title[" + story.id + "]' value='" + story.title + "' />");
    $row.find('.form-control-static[data-name="status"]').addClass('status-' + story.rawStatus);
    if($branch.length > 0)
    {
        var branches = typeof branchTagOption[story.product] == 'undefined' ? [] : branchTagOption[story.product];

        $branch.find('.picker-box').on('inited', function(e, info)
        {
            let $picker = info[0];
            let options = $picker.options;
            let items   = [{text: '', value: ''}];
            for(let branch in branches)
            {
                if(branch == '') continue;
                items.push({text: branches[branch], value: branch});
            }
            options.items        = items;
            options.onChange     = function(){loadBranches(story.product, this)};
            options.defaultValue = story.branch;
            options.required     = true;

            $picker.render(options);
        });
    }

    if($module.length > 0)
    {
        var modules = typeof moduleList[story.id] == 'undefined' ? ['/'] : moduleList[story.id];

        $module.find('.picker-box').on('inited', function(e, info)
        {
            let $picker = info[0];
            let options = $picker.options;
            let items   = [];
            for(let module in modules) items.push({text: modules[module], value: module});
            options.items = items;
            options.defaultValue = story.module;

            $picker.render(options);
        });
    }

    if($plan.length > 0)
    {
        var plans = typeof(planGroups[story.product]) == 'undefined' ? [] : planGroups[story.product];
        var plans = typeof(plans[story.branch]) == 'undefined'       ? [] : plans[story.branch];

        $plan.find('.picker-box').on('inited', function(e, info)
        {
            let $picker = info[0];
            let options = $picker.options;
            let items   = [];
            for(let plan in plans)
            {
                if(plan == '') continue;
                items.push({text: plans[plan], value: plan});
            }
            options.items = items;
            options.defaultValue = story.plan;
            options.multiple = story.type != 'story' ? true : false;

            $picker.render(options);
        });

        $row.attr('type', story.type);
    }

    if(story.source == 'meeting' || story.source == 'researchreport')
    {
        objects = story.source == 'meeting' ? meetings : researchReports;
        var $sourceNoteTd = $row.find('.form-batch-control[data-name="sourceNote"]');
        $sourceNoteTd.html("<div class='form-control picker-box' data-name='source'></div>");

        items = [];
        for(let note in objects) items.push({text:objects[note], value:note});
        $sourceNoteTd.find('.picker-box').render({items: items, name: 'sourceNote[' + story.id + ']', defaultValue: story.sourceNote});
    }
};

window.loadBranches = function(product, obj)
{
    $this  = $(obj);
    branch = $this.val();

    var index           = $this.closest('tr').data('index');
    var storyID         = $this.closest('tr').find('.form-batch-input[data-name="storyIdList"]').val();
    var $module         = $this.closest('tr').find('.form-batch-control[data-name="module"]');
    var currentModuleID = $module.val();
    var moduleLink      = $.createLink('tree', 'ajaxGetOptionMenu', 'productID=' + product + '&viewtype=story&branch=' + branch + '&rootModuleID=0&returnType=items&fieldID=' + storyID + '&extra=nodeleted&currentModuleID=' + currentModuleID);
    $.get(moduleLink, function(items)
    {
        $picker = $this.closest('tr').find('.picker-box[data-name="module"]').zui('picker');
        options = $picker.options;
        options.items = items;
        $this.closest('tr').find('.picker-box[data-name="module"]').render(options);
    });

    var $plan    = $this.closest('tr').find('.form-batch-control[data-name="plan"]');
    var planID   = $plan.val();
    var planLink = $.createLink('product', 'ajaxGetPlans', 'productID=' + product + '&branch=' + branch + '&planID=' + planID + '&fieldID=' + storyID + '&needCreate=false&expired=&param=skipParent');
    $.get(planLink, function(items)
    {
        $picker = $this.closest('tr').find('.picker-box[data-name="plan"]').zui('picker');
        options = $picker.options;
        options.items = items;
        $this.closest('tr').find('.picker-box[data-name="plan"]').render(options);
    });
}

window.setDuplicateAndChild = function(resolution, storyID)
{
    if(resolution == 'duplicate')
    {
        $('#duplicateStoryBox' + storyID).removeClass('hidden');
    }
    else
    {
        $('#duplicateStoryBox' + storyID).addClass('hidden');
    }
};

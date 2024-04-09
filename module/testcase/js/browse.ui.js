$(document).off('click', '.batch-btn').on('click', '.batch-btn', function()
{
    const dtable = zui.DTable.query($(this).target);
    const checkedList = dtable.$.getChecks();
    if(!checkedList.length) return;

    const url  = $(this).data('url');
    const form = new FormData();

    checkedList.forEach((id) => {
        const data = dtable.$.getRowInfo(id).data;
        if(data.isScene)  form.append('sceneIdList[]', data.caseID);
        if(!data.isScene) form.append('caseIdList[]',  data.caseID);
    });

    if($(this).hasClass('ajax-btn'))
    {
        if($(this).hasClass('batch-delete-btn'))
        {
            zui.Modal.confirm(confirmBatchDeleteSceneCase).then((res) => {if(res) $.ajaxSubmit({url, data:form});});
        }
        else
        {
            $.ajaxSubmit({url, data:form});
        }
    }
    else
    {
        postAndLoadPage(url, form);
    }
});

window.onSortEnd = function(from, to, type)
{
    if(!from || !to) return false;

    const url  = $.createLink('testcase', 'updateOrder');
    const form = new FormData();

    form.append('sourceID',    from.data.caseID);
    form.append('sourceOrder', from.data.sort);
    form.append('targetID',    to.data.caseID);
    form.append('targetOrder', to.data.sort);
    form.append('type', type);
    $.ajaxSubmit({url, data:form});
}

/**
 * 切换显示所有用例和自动化用例。
 * Toggles between displaying all cases and automation cases.
 *
 * @param  event $event
 * @access public
 * @return void
 */
window.toggleOnlyAutoCase = function(event)
{
    const onlyAutoCase = $(event.target).prop('checked') ? 1 : 0;
    $.cookie.set('onlyAutoCase', onlyAutoCase, {expires:config.cookieLife, path:config.webRoot});
    loadCurrentPage();
}

/**
 * 标题列显示额外的内容。
 * Display extra content in the title column.
 *
 * @param  object result
 * @param  object info
 * @access public
 * @return object
 */
window.onRenderCell = function(result, {row, col})
{
    if(result)
    {
        if(col.name == 'caseID' && row.data.isScene)
        {
            result.shift(); // 移除场景ID
        }
        if(col.name == 'title')
        {
            const data = row.data;
            const module = this.options.customData.modules[data.module];
            if(data.color) result[0].props.style = 'color: ' + data.color;
            if(data.isScene) // 场景
            {
                result.shift(); // 移除带链接的场景名称
                result.push({html: data.title}); // 添加不带链接的场景名称
                if(data.grade == 1 && module) result.unshift({html: '<span class="label gray-pale rounded-full">' + module + '</span>'}); // 顶级场景添加模块标签
                result.unshift({html: '<span class="label gray-300-outline text-gray rounded-full">' + scene + '</span>'}); // 添加场景标签
                if(!this.options.customData.isOnlyScene && data.hasCase == false) result.push({html: '<span class="text-gray">(' + noCase + ')</span>'}); // 添加暂无用例标签
            }
            else // 用例
            {
                if(data.auto == 'auto') result.unshift({html: '<span class="label gray-pale rounded-full">' + automated + '</span>'}); // 添加自动化标签
                if(module) result.unshift({html: '<span class="label gray-pale rounded-full">' + module + '</span>'}); // 添加模块标签
            }
        }
        if(col.name == 'pri' && row.data.isScene)
        {
            result.shift(); // 移除场景优先级
        }
    }

    if(row.data.lastEditedDate == '0000-00-00 00:00:00') row.data.lastEditedDate = '';
    if(row.data.reviewedDate == '0000-00-00') row.data.reviewedDate = '';

    return result;
}

/**
 * 计算表格信息的统计。
 * Set summary for table footer.
 *
 * @param  element element
 * @param  array   checks
 * @access public
 * @return object
 */
window.setStatistics = function(element, checks)
{
    if(checks.length)
    {
        caseCount    = 0;
        runCaseCount = 0;
        checks.forEach((id) => {
            const scene = element.getRowInfo(id).data;
            if(scene.isScene == false)
            {
                caseCount ++;
                if(scene.lastRunResult != '') runCaseCount ++;
            }
        });
        return zui.formatString(checkedSummary, {
            checked: caseCount,
            run: runCaseCount
        });
    }

    return element.options.customData.pageSummary;
}

/**
 * Get selected case id list.
 *
 * @access public
 * @return void
 */
function getCheckedCaseIdList()
{
    let caseIdList = '';

    const dtable = zui.DTable.query('#table-testcase-browse');
    $.each(dtable.$.getChecks(), function(index, caseID)
    {
        if(index > 0) caseIdList += ',';
        caseIdList += caseID;
    });
    $('#caseIdList').val(caseIdList);
}

/**
 * Check ztf script run result.
 *
 * @param  e
 * @access public
 * @return void
 */
window.checkZtf = function(e)
{
    e.preventDefault();
    e.stopPropagation();

    const url = $(this).attr('href');
    $.get(url, function(result)
    {
        const load = result.load;
        if(!load || typeof load == 'string')
        {
            zui.Modal.open({url: load, size: 'lg', replace: true});
            return false;
        }

        zui.Modal.confirm(load.confirm).then((res) => {
            if(!res) return loadModal(load.canceled, null, {size: 'lg'});

            $.post(load.confirmed, {}, function(result)
            {
                result = JSON.parse(result);
                if(result.result == 'fail') return zui.Modal.alert(result.message);

                loadModal(load.canceled, null, {size: 'lg'});
            });
        });
    }, 'json');
}

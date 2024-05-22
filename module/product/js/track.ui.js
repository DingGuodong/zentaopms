window.getItem = function(info)
{
    const col = info.col;
    if(col.indexOf('epic') != -1 || col.indexOf('requirement') != -1 || col.indexOf('story') != -1)
    {
        info.item.title   = {html: "<div class='line-clamp-2'><span class='align-sub pri-" + info.item.pri + "'>" + langStoryPriList[info.item.pri] + "</span> <span class='title' title='" + info.item.title + "'>" + info.item.title + '</span></div>'}
        info.item.content = [];
        info.item.content.push({html: "<div class='status-" + info.item.status + "'>" + langStoryStatusList[info.item.status] + "</div>"});
        info.item.content.push({html: "<div style='color: var(--color-gray-600);'>" + langStoryStageList[info.item.stage] + '</div>'})
    }
    else if(col == 'project' || col == 'execution')
    {
        let delayHtml = '';
        let titleHtml = info.item.title;
        if(info.item.delay > 0) delayHtml = "<span class='label danger-pale nowrap pull-left absolute right-0 bottom-0'>" + langProjectStatusList['delay'] + "</span>";
        if(col == 'execution') titleHtml = "<a href='" + $.createLink('execution', 'task', 'executionID=' + info.item.id) + "'>" + info.item.title + "</a>";
        info.item.title   = {html: "<div class='relative'><span class='title line-clamp-2' title='" + info.item.title + "'>" + titleHtml + '</span>' + delayHtml + '</div>'}

        info.item.content = [];
        info.item.content.push({html: "<div class='status-" + info.item.status + "'>" + langProjectStatusList[info.item.status] + "</div>"});
        info.item.content.push({component: 'ProgressCircle', props: {percent: info.item.progress, size: 24}});
    }
    else if(col == 'task')
    {
        console.log(info);
        titleHtml = "<a href='" + $.createLink('task', 'view', 'taskID=' + info.item.id) + "' data-toggle='modal' data-size='lg'>" + info.item.title + "</a>";
        info.item.title   = {html: "<div class='line-clamp-2'><span class='align-sub pri-" + info.item.pri + "'>" + langTaskPriList[info.item.pri] + "</span> <span class='title' title='" + info.item.title + "'>" + titleHtml + '</span></div>'}
        info.item.content = [];
        if(info.item.parent == '-1') info.item.content.push({html: "<span class='label cursor-pointer primary rounded-xl is-collapsed' onclick='toggleChildren(this, " + info.item.id + ")'>" + langChildren + " <span class='toggle-icon ml-1'></span></span>"});
        info.item.content.push({html: "<div class='status-" + info.item.status + "'>" + langTaskStatusList[info.item.status] + "</div>"});
        if(info.item.assignedTo) info.item.content.push({html: "<i class='icon icon-hand-right'></i> " + (typeof(users[info.item.assignedTo]) ? users[info.item.assignedTo] : info.item.assignedTo)});
        info.item.content.push({component: 'ProgressCircle', props: {percent: info.item.progress, size: 24}});
    }
}

window.itemRender = function(info)
{
    if(info.col == 'task' && info.item.parent > '0') info.item.className.push('hidden parent-' + info.item.parent);
}

window.toggleChildren = function(obj, parentID)
{
  console.log(obj);
    if($(obj).hasClass('is-expanded'))
    {
        $(obj).removeClass('is-expanded').addClass('is-collapsed');
        $('.parent-' + parentID).addClass('hidden')
    }
    else
    {
        $(obj).removeClass('is-collapsed').addClass('is-expanded');
        $('.parent-' + parentID).removeClass('hidden')
    }
}

window.canDrop = function(){ return false;}

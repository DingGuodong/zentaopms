var iframeHeight  = 0;
var sidebarHeight = 0;
var tabTemp;
var parentTree = [];

/* Close tab. */
$('#monacoTabs').on('click', '.monaco-close', function()
{
    var eleId    = $(this).parent().attr('href');
    var tabsEle  = $(this).parent().parent().parent();
    var isActive = $(this).parent().hasClass('active');

    $(this).parent().parent().remove();
    $(eleId).remove();
    $('#' + eleId.substring(5)).parent().removeClass('selected');
    if(isActive) tabsEle.children().last().find('a').trigger('click');
});

window.afterPageUpdate = function()
{
    setTimeout(function()
    {
        var fileAsId = file.replace(/=/g, '-');
        /* Resize moaco height. */
        $('#monacoTree').css('height', getSidebarHeight() - 8 + 'px');
        /* Init tab template. */
        if(!tabTemp) tabTemp = $('#monacoTabs ul li').first().clone();
        
        /* Load default tab content. */
        var height = getIframeHeight();
        $('#tab-' + fileAsId).html("<iframe class='repo-iframe' src='" + $.createLink('repo', 'ajaxGetEditorContent', urlParams.replace('%s', fileAsId)) + "' width='100%' height='" + height + "' scrolling='no'></iframe>")
        
        /* Select default tree item. */
        const currentElement = findItemInTreeItems(tree, fileAsId, 0);
        $('#' + currentElement.id).parent().addClass('selected');
        expandTree();
    }, 200);
};

/**
 * 点击左侧菜单打开详情tab。
 * Open new tab when click tree item.
 *
 * @access public
 * @return void
 */
window.treeClick = function(info)
{
    if (info.item.items && info.item.items.length > 0) return;
    $('#' + info.item.id).parent().addClass('selected');
    openTab(info.item.key, info.item.text);
    arrowTabs('fileTabs', -2);
}

/**
 * 打开新tab。
 * Open new tab.
 *
 * @access public
 * @return void
 */
function openTab(entry, name)
{
    var eleId   = 'tab-' + entry.replace(/=/g, '-');
    var element = document.getElementById(eleId);
    if (element)
    {
        $("a[href='" + '#' + eleId + "']").trigger('click');
        return;
    }

    var newTab = tabTemp.clone();
    newTab.find('a').attr('href', '#' + eleId);
    newTab.find('span').text(name);
    $('#monacoTabs .nav-tabs').append(newTab);

    var height = getIframeHeight();
    $('#monacoTabs .tab-content').append("<div id='" + eleId + "' class='tab-pane active in'><iframe class='repo-iframe' src='" + $.createLink('repo', 'ajaxGetEditorContent', urlParams.replace('%s', entry)) + "' width='100%' height='" + height + "' scrolling='no'></iframe></div>")

    setTimeout(() => {
        $("a[href='" + '#' + eleId + "']").trigger('click');
    }, 100);
}

/**
 * 在当前页面用modal加载链接。
 * Load link object page.
 *
 * @param  string $link
 * @access public
 * @return void
 */
window.loadLinkPage = function(link)
{
    $('#linkObject').attr('href', link);
    $('#linkObject').trigger('click');
}
var iframeHeight  = 0;
var sidebarHeight = 0;
var tabTemp;
var diffAppose = $.cookie.get('renderSideBySide');;

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
        $('#tab-' + fileAsId).html("<iframe class='repo-iframe' src='" + $.createLink('repo', 'ajaxGetDiffEditorContent', urlParams.replace('%s', fileAsId)) + "' width='100%' height='" + height + "' scrolling='no'></iframe>")
        
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
 *  Change code encoding.
 *
 * @param  string encoding
 * @access public
 * @return void
 */
function changeEncoding(encoding)
{
    $('#encoding').val(encoding);
    $('#encoding').parents('form').submit();
}

/**
 *  Html code decode.
 *
 * @param  string str
 * @access public
 * @return string
 */
function htmlspecialchars_decode(str){
    str = str.replace(/&amp;/g, '&');
    str = str.replace(/&lt;/g, '<');
    str = str.replace(/&gt;/g, '>');
    str = str.replace(/&quot;/g, "''");
    str = str.replace(/&#039;/g, "'");
    return str;
}

/**
 * Get diffs by file name.
 *
 * @param  string fileName
 * @access public
 * @return object
 */
window.getDiffs = function(fileName)
{
    if(fileName.indexOf('./') === 0) fileName = fileName.substring(2);

    var result = {
        'code': {'new': '', 'old': ''},
        'line': {'new': [], 'old': []}
    };
    $.each(diffs, function(i, diff)
    {
        if(diff.fileName == fileName)
        {
            if(!diff.contents || typeof diff.contents[0].lines != 'object') return result;

            $.each(diff.contents, function(k, content)
            {
                var lines = content.lines;
                $.each(lines, function(l, code)
                {
                    if(code.type == 'all' || code.type == 'new')
                    {
                        result.code.new += htmlspecialchars_decode(code.line.substring(1)) + "\n";
                        result.line.new.push(parseInt(code.newlc));
                    }

                    if(code.type == 'all' || code.type == 'old')
                    {
                        result.code.old += htmlspecialchars_decode(code.line.substring(1)) + "\n";
                        result.line.old.push(parseInt(code.oldlc));
                    }
                })
            })
            return result;
        }
    });

    return result;
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
    $('#monacoTabs .tab-content').append("<div id='" + eleId + "' class='tab-pane active in'><iframe class='repo-iframe' src='" + $.createLink('repo', 'ajaxGetDiffEditorContent', urlParams.replace('%s', entry)) + "' width='100%' height='" + height + "' scrolling='no'></iframe></div>")

    setTimeout(() => {
        $("a[href='" + '#' + eleId + "']").trigger('click');
        updateEditorInline('#' + eleId);
    }, 100);
}

function updateEditorInline(eleId)
{
    $.cookie.set('renderSideBySide', diffAppose);
    if(typeof  $(eleId + ' iframe')[0].contentWindow.updateEditorInline == 'function')
    {
        $(eleId + ' iframe')[0].contentWindow.updateEditorInline(diffAppose);
    }
}

$('#monacoTabs .nav-item a').on('click', function()
{
    var eleId = $(this).attr('href');
    $(eleId + ' iframe')[0].contentWindow.updateEditorInline(diffAppose);
});

$(document).ready(function()
{
    if(diffAppose)
    {
        $('.dropdown-menu #inline').show();
        $('.dropdown-menu #appose').hide();
    }
    else
    {
        $('.dropdown-menu #appose').show();
        $('.dropdown-menu #inline').hide();
    }

    $('.btn-left').on('click', function()  {arrowTabs('fileTabs', 1);});
    $('.btn-right').on('click', function() {arrowTabs('fileTabs', -2);});
});

$('.inline-appose').on('click', function()
{
    $('.inline-appose').hide();
    diffAppose = !diffAppose;
    if(diffAppose)
    {
        $('.dropdown-menu #inline').show();
    }
    else
    {
        $('.dropdown-menu #appose').show();
    }
    var tabID = $('#monacoTabs .nav-item .active').attr('href');
    updateEditorInline(tabID);
    return;
});

$(".label-exchange").on('click', function()
{
    var newDiffLink = diffLink.replace('{oldRevision}', newRevision);
    newDiffLink     = newDiffLink.replace('{newRevision}', oldRevision);
    openUrl(newDiffLink);
});

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
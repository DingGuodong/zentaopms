checkServiceStatus();
function checkServiceStatus(){
    toggleLoading('#serviceContent', true);
    $.get($.createLink('zanode', 'ajaxGetServiceStatus', 'nodeID=' + nodeID), function(response)
    {
        var resultData = JSON.parse(response);
        var isSuccess = true

        for (var key in resultData.data)
        {
            if(key == "ZTF")
            {
                if(resultData.data[key] == 'ready')
                {
                    $('.dot-ztf').removeClass("text-danger")
                    $('.dot-ztf').addClass("text-success")
                }
                else{
                    $('.dot-ztf').removeClass("text-success")
                    $('.dot-ztf').addClass("text-danger")
                }

                if(resultData.data[key] == 'ready' || resultData.data[key] == 'not_available')
                {
                    $('.ztf-status').text(zanodeLang.init[resultData.data[key]])
                    $('.ztf-install').text('');
                    $('.ztf-install-icon').hide();
                }
                else
                {
                    if(resultData.data[key] == 'unknown')
                    {
                        if(hostType == 'physics')
                        {
                            $('.ztf-status').text(zanodeLang.init.not_install)
                        }
                        else
                        {
                            $('.ztf-status').text(zanodeLang.init.unknown)
                        }
                    }
                    else
                    {
                        $('.ztf-status').text(zanodeLang.initializing)
                    }
                    $('.ztf-install').text(zanodeLang.install);
                }
            }
            else if(key == "node")
            {
                if(resultData.data[key] && zanodeLang.statusList[nodeStatus] != zanodeLang.statusList[resultData.data[key]])
                {
                    window.location.reload();
                }
            }
            else
            {
                if(resultData.data[key] == 'ready')
                {
                    $('.dot-zenagent').removeClass("text-danger")
                    $('.dot-zenagent').addClass("text-success")
                }
                else{
                    $('.dot-zenagent').removeClass("text-success")
                    $('.dot-zenagent').addClass("text-danger")
                }
                $('.zenagent-status').text(zanodeLang.init[resultData.data[key]])
                if(resultData.data[key] == 'ready')
                {
                    $('.node-init-install').removeClass('hidden');
                }
                else
                {
                    if(resultData.data[key] == 'unknown')
                    {
                        $('.ztf-zenagent').text(zanodeLang.init.unknown)
                    }
                    else
                    {
                        $('.zenagent-status').text(zanodeLang.initializing)
                    }
                }
            }
            if(resultData.data[key] !== 'ready' && key != 'node')
            {
                isSuccess = false
            }
        };

        if(!isSuccess)
        {
            $('.init-fail').removeClass('hidden');
            $('.init-success').addClass('hidden');
        }
        else
        {
            clearInterval(checkInterval)
            $('.init-success').removeClass('hidden');
            $('.init-fail').addClass('hidden');
        }
        setTimeout(function() {
            toggleLoading('#serviceContent', false);
            $(".service-status, .status-notice").removeClass('hidden')
        }, 500);
    });
    return
}

function sshCopy()
{
    var copyText = $('#ssh-copy');
    copyText.removeClass('hidden');
    document.getElementById('ssh-copy').select();
    document.execCommand("Copy");
    copyText.addClass('hidden');
    $('.btn-ssh-copy').tooltip({
        trigger: 'click',
        placement: 'bottom',
        title: zanodeLang.copied,
        tipClass: 'tooltip-success'
    });

    $(this).tooltip('show');
    var that = this;
    setTimeout(function()
    {
        $(that).tooltip('hide')
    }, 2000)
}

function pwdCopy()
{
    var copyText = $('#pwd-copy');
    copyText.show();
    document.getElementById('pwd-copy').select();
    document.execCommand("Copy");
    copyText.hide();
    $('.btn-pwd-copy').tooltip({
        trigger: 'click',
        placement: 'bottom',
        title: zanodeLang.copied,
        tipClass: 'tooltip-success'
    });

    $(this).tooltip('show');
    var that = this;
    setTimeout(function()
    {
        $(that).tooltip('hide')
    }, 2000)
}

function pwdShow()
{
    var pwd     = $('#pwd-copy').text();
    var pwdStar = "******".padEnd(pwd.length, '*')
    var html    = $('.btn-pwd-show').html()
    if(html.indexOf('icon-eye-off') != -1)
    {
        $('#pwd-text').text(pwdStar)
        $('.btn-pwd-show').html("<i class='icon-common-eye icon-eye' title='" + zanodeLang.showPwd +  "'></i>")
    }
    else
    {
        $('#pwd-text').text(pwd)
        $('.btn-pwd-show').html("<i class='icon-common-eye icon-eye-off' title='" + zanodeLang.hidePwd +  "'></i>")
    }
}

$(function()
{
    $('.create-snapshot i').replaceWith("<img src='static/svg/snapshot.svg' />");
});

window.switchAccount = function(account)
{
    link = $.createLink('user', method, 'account=' + account);
    if(method == 'dynamic') link = $.createLink('user', method, 'account=' + account + '&period=' + pageParams.period);
    if(method == 'todo')    link = $.createLink('user', method, 'account=' + account + '&type=' + pageParams.type);
    if(method == 'story')   link = $.createLink('user', method, 'account=' + account + '&storyType=' + pageParams.storyType);

    loadPage(link);
};

function computePasswordStrength(password)
{
    if(password.length == 0) return 0;

    var strength = 0;
    var length   = password.length;

    var complexity  = new Array();
    for(i = 0; i < length; i++)
    {
        letter = password.charAt(i);
        var asc = letter.charCodeAt();
        if(asc >= 48 && asc <= 57)
        {
            complexity[0] = 1;
        }
        else if((asc >= 65 && asc <= 90))
        {
            complexity[1] = 2;
        }
        else if(asc >= 97 && asc <= 122)
        {
            complexity[2] = 4;
        }
        else
        {
            complexity[3] = 8;
        }
    }

    var sumComplexity = 0;
    for(i in complexity) sumComplexity += complexity[i];

    if((sumComplexity == 7 || sumComplexity == 15) && password.length >= 6) strength = 1;
    if(sumComplexity == 15 && password.length >= 10) strength = 2;

    return strength;
}

/**
 * Switch account
 *
 * @param  string $account
 * @param  string $method
 * @access public
 * @return void
 */
$(document).ready(function()
{
    var verifyEncrypted = false;
    $('#verifyPassword').on('change', function(){verifyEncrypted = false});
    $('#verifyPassword').closest('form').find('button[type="submit"]').on('click', function()
    {
        var password = $('input#verifyPassword').val().trim();
        var rand     = $('input[name=verifyRand]').val();
        if(!verifyEncrypted && password) $('input#verifyPassword').val(md5(md5(password) + rand));
        verifyEncrypted = true;
    });
});

/**
 * Check password strength.
 *
 * @param  event  $event
 * @access public
 * @return void
 */
function checkPassword(event)
{
    let password = $(event.target).val();
    $('#passwordStrength').html(password == '' ? '' : passwordStrengthList[computePasswordStrength(password)]);
    if(password == '')
    {
        $('#passwordStrength').addClass('hidden');
    }
    else
    {
        $('#passwordStrength').removeClass('hidden');
    }
}

/**
 * Update groups when visions change.
 *
 * @param  event  $event
 * @access public
 * @return void
 */
function changeVision(event)
{
    var visions = [];
    $('input[name="visions[]"]:checked').each(function()
    {
        visions.push($(this).val());
    });

    const link  = $.createLink('user', 'ajaxGetGroup', 'visions=' + visions);
    $.get(link, function(data)
    {
        let group        = $('[name^="group"]').val();
        let $groupPicker = $('[name^="group"]').zui('picker');
        if(data)
        {
            data = JSON.parse(data);
            $groupPicker.render({items: data});
            $groupPicker.$.changeState({value: group});
        }
    });
}

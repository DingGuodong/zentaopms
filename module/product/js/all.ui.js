window.footerGenerator = function()
{
    const count = this.layout.allRows.filter((x) => x.data.type === "product").length;
    const statistic = langSummary.replace('%s', ' ' + count + ' ');
    return [{children: statistic, className: "text-dark"}, "flex", "pager"];
}

window.renderReleaseCountCell = function(result, {col, row})
{
    if(!col || !row || col.name !== 'releases') return result;

    var changed = row.data.releases - row.data.releasesOld;

    if(changed === 0) result[0] = 0;
    if(changed > 0)   result[0] = {html: row.data.releases + ' <span class="label size-sm circle primary-pale bd-primary">+' + changed + '</span>'};
    if(changed < 0)   result[0] = {html: row.data.releases + ' <span class="label size-sm circle warning-pale bd-warning">' + changed + '</span>'};

    return result;
}

window.programMenuOnClick = function(data, url)
{
    location.href = url.replace('%d', data.item.key);
}

onRenderPage(function(info)
{
    loadCurrentPage('#mainMenu>*');
    return false;
});

/**
 * Submit data to product batch edit page by html form while click on the batch edit button.
 *
 * @param  object event
 * @access public
 * @return void
 */
onClickBatchEdit = function(event)
{
    /* Get checked product ID list. */
    const idList  = new Array();
    const rowList = $('#dtable').find('.dtable-row');

    Object.keys(rowList).forEach(function(index)
    {
        const row = $(rowList[index]);

        const checkboxList = row.find('div.dtable-checkbox');
        if(checkboxList.length !== 1) return;
        if(!$(checkboxList[0]).hasClass('checked')) return;

        const id  = row.data('id');
        if( !id || id === 'HEADER') return;

        idList.push(id);
    });

    if(idList.length === 0) return;

    /* Create form. */
    const f = document.createElement("form");
    f.action = $(event.target).attr('href');
    f.method = "POST";

    /* Create element to carry data. */
    idList.forEach(function(id)
    {
        const item = document.createElement('input');
        item.name  = 'productIDList[]';
        item.value = id;

        f.appendChild(item);
    });

    /* Append form to body. */
    document.body.appendChild(f);

    f.submit();
}

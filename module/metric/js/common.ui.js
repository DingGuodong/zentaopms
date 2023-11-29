window.addUnit = function(e)
{
    if($(e.target).prop('checked'))
    {
        $('#unitBox').addClass('hidden');
        $('#addUnitBox').removeClass('hidden');
        $("[name^='customUnit']").prop('checked', true);
    }
    else
    {
        $('#unitBox').removeClass('hidden');
        $('#addUnitBox').addClass('hidden');
        $("[name^='customUnit']").prop('checked', false);
    }
}

window.renderDTableCell = function(result, {row, col})
{
    var commonWidth = {
        scope: 160,
        date: 96,
        value: 96,
        calcTime: 128,
    };
    var width = 480;

    var cols = Object.keys(row.data);

    var cellWidth = {};
    cols.forEach(function(col) {
        if(commonWidth[col]) {
            cellWidth[col] = commonWidth[col];
        }
    });

    colsWidth = Object.values(cellWidth).reduce((a, b) => a + b, 0);
    Object.keys(cellWidth).forEach(function(col) {
        cellWidth[col] = Math.floor(cellWidth[col] / colsWidth * width);
    });


    if(Array.isArray(row.data[col.name]))
    {
        var value = row.data[col.name][0];
        var title = updateTimeTip.replace('%s', row.data[col.name][1]);
    }
    else
    {
         var title = value = row.data[col.name];
    }
    var html  = `<span class="cell-ellipsis" style="width: ${cellWidth[col.name] - 24}px;" title="${title}">${value}</span>`;
    result[0] = {html: html};

    return result;
}

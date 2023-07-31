window.renderCell = function(result, {col, row})
{
    if(col.name == 'path')
    {
        result[0] = '';
        if(row.data.pathType == 'scmPath' && row.data.scmPath)
        {
            const colorStyle = row.data.scmPath.indexOf('http') === 0 ? "style='color:#2463c7;'" : '';
            let scmPathHtml  = '';
            scmPathHtml += "<div><i class='icon icon-file-code' " + colorStyle + "title='" +  scmPathTip + "'></i> ";
            scmPathHtml += "<span title='" + row.data.scmPath + "'>";
            scmPathHtml += colorStyle.length ? "<a target='_blank' " + colorStyle + " href='" + row.data.filePath + "'>" + row.data.scmPath + "</a>" : row.data.scmPath;
            scmPathHtml += '</span></div>';

            result[result.length] = {html: scmPathHtml};
        }

        if(row.data.pathType == 'filePath' && row.data.filePath)
        {
            const colorStyle = row.data.filePath.indexOf('http') === 0 ? "style='color:#2463c7;'" : '';
            let filePathHtml  = '';
            filePathHtml += "<div><i class='icon icon-download' " + colorStyle + "title='" +  filePathTip + "'></i> ";
            filePathHtml += "<span title='" + row.data.filePath + "'>";
            filePathHtml += colorStyle.length ? "<a target='_blank' " + colorStyle + " href='" + row.data.filePath + "'>" + row.data.filePath + "</a>" : row.data.filePath;
            filePathHtml += '</span></div>';

            result[result.length] = {html: filePathHtml};
        }
        return result;
    }

    if(col.name == 'name' && row.data.execution == 0)
    {
        result[result.length] = {html: "<span class='icon icon-code-fork text-gray' title='" + integratedTip + "'></span>"};
        return result;
    }

    if(col.name == 'execution')
    {
        let executionHtml = "<span title='" + row.data.executionName + "'>" + row.data.executionName + '</span>';
        if(row.data.executionDeleted == 1) executionHtml += " <span class='label label-danger'>" + deletedTip + '</span>';
        result[0] = {html: executionHtml};
        return result;
    }

    return result;
}

/**
 * Change product.
 *
 * @access public
 * @return void
 */
window.changeProduct = function()
{
    const link = changeProductLink.replace('{productID}', $(this).val());
    loadPage(link);
}

/**
 * 合并单元格。
 * cell span in the column.
 *
 * @param  object cell
 * @access public
 * @return object
 */
window.getCellSpan = function(cell)
{
    if(['id', 'name', 'productName', 'branchName', 'builder', 'date', 'actions'].includes(cell.col.name) && cell.row.data.rowspan)
    {
        return {rowSpan: cell.row.data.rowspan};
    }

    if(cell.col.name == 'path' && cell.row.data.pathRowspan)
    {
        return {rowSpan: cell.row.data.pathRowspan};
    }

    if(cell.col.name == 'execution' && cell.row.data.executionRowspan)
    {
        return {rowSpan: cell.row.data.executionRowspan};
    }
}

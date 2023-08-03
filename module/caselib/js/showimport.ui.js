window.handleRenderRow = function($row, index, row)
{
    let stepDesc   = "<input class='hidden' type='text' name='lib[" + row.id + "]' value='" + libID + "'/>";
    let stepExpect = '';

    const descs  = stepData[index + 1]['desc'];
    const expect = stepData[index + 1]['expect'];
    if(descs.length)
    {
        $.each(descs, function(id, desc)
        {
            if(!desc.content) return;
            stepDesc   += "<div class='flex col'><div class='cell flex border p-2'><div class='cell center'><input class='hidden' type='text' name='stepType[" + row.id + "][" + desc.number + "]' value='" + desc.type + "' /><span>" + desc.number + "、</span></div><div class='cell center flex-1'><textarea class='form-control form-batch-input' rows='10' name='desc[" + row.id + "][" + desc.number + "]' style='min-height: 32px; height:2rem;'>" + desc.content + "</textarea></div></div></div>";
            stepExpect += "<div class='flex col'><div class='cell flex border p-2'><textarea class='form-control form-batch-input " + (desc.type != 'group' ? '' : 'disabled') + "'" + (desc.type != 'group' ? '' : 'readonly=readonly') + "' rows='10' name='expect[" + row.id + "][" + desc.number + "]' style='min-height: 32px; height:2rem;'>" + (expect[id]['content'] ? expect[id]['content'] : '') + "</textarea></div></div></div>";
        })
    }
    else
    {
        stepDesc   += "<div class='flex col'><div class='cell flex border p-2'><div class='cell center'><input class='hidden' type='text' name='stepType[" + row.id + "][1]' value='step' /><span>1、</span></div><div class='cell center flex-1'><textarea class='form-control form-batch-input' rows='10' name='desc[" + row.id + "][1]' style='min-height: 32px; height:2rem;'></textarea></div></div></div>";
        stepExpect += "<div class='flex col'><div class='cell flex border p-2'><textarea class='form-control form-batch-input' rows='10' name='expect[" + row.id + "][1]'  style='min-height: 32px; height:2rem;'></textarea></div></div>";
    }


    $row.find('td[data-name=stepDesc]').html(stepDesc);
    $row.find('td[data-name=stepExpect]').html(stepExpect);
}

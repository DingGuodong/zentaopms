$(() =>
{
    const btn = document.querySelector('#ajax-save');
    btn.addEventListener('click', () => {
        const formData = [];
        let index = 0;
        const types = ['left', 'no', 'right'];
        const getSelector = (value) => `[data-zin-gid="${formGID}"] .drag-ul.${value}-cols`;
        const colsList = types.map(x => document.querySelector(getSelector(x)));

        for(let i = 0; i < colsList.length; i++)
        {
            const cols = colsList[i];
            if(!cols) continue;

            const children = Array.from(cols.children);
            for(let j = 0; j < children.length; j++)
            {
                const li = children[j];
                const checkbox = li.querySelector('input[type="checkbox"]');
                const input = li.querySelector('input[type="text"]');
                formData.push({
                    id: li.dataset.key,
                    order: ++index,
                    show: checkbox.checked,
                    width: input.value,
                    fixed: types[i],
                });
            }
        }

        $.ajaxSubmit({
            url: ajaxSaveUrl,

            type: 'POST',

            contentType: 'application/json',

            data: {fields: JSON.stringify(formData)},

            closeModal: true,
        });
    });
});

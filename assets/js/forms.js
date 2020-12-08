$(() => {
    const $collectionHolder = $('ul.embedded-form');

    $collectionHolder.find('li').each(() => {
        addFormDeleteLink($(this));
    });

    $collectionHolder.data('index', $collectionHolder.find('input').length);

    $('body').on('click', '.add_item_link', (e) => {
        const $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
        addFormToCollection($collectionHolderClass);
    });

    $(document).on('change', 'input.custom-file-input[type="file"]', (e) => {
        const fileName = $(`#${e.target.id}`).val();
        $(`#${e.target.id}`).next('.custom-file-label').html(fileName);
    });
});

function addFormToCollection($collectionHolderClass) {
    const $collectionHolder = $('.' + $collectionHolderClass);

    const prototype = $collectionHolder.data('prototype');

    const index = $collectionHolder.data('index');

    let newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    let $newFormLi = $('<li></li>').append(newForm);
    $collectionHolder.append($newFormLi);

    addFormDeleteLink($newFormLi);
}

function addFormDeleteLink($formLi) {
    const $removeFormButton = $('<button class="btn btn-warning btn-sm" type="button">Delete</button>');
    $formLi.append($removeFormButton);

    $removeFormButton.on('click', (e) => {
        $formLi.remove();
    });
}
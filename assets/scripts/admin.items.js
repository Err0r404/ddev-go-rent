import htmx from 'htmx.org';

let refreshItems = function () {
    const pricesUl = document.querySelector('#admin-item-prices');
    const addItemBtn = document.querySelector('button.btn-add-price');

    if (pricesUl && addItemBtn) {
        console.log('admin.items.js: pricesUl && addItemBtn');

        const addPriceFormDeleteLink = (item) => {
            const divWrapper = document.createElement('div');
            divWrapper.classList.add('text-end');

            // Add div wrapper to first div in item
            item.querySelector('div:first-child').appendChild(divWrapper);

            const removeFormButton = document.createElement('button');
            removeFormButton.classList.add('btn', 'btn-sm', 'btn-outline-danger', 'py-0', 'px-2');
            removeFormButton.innerText = 'Delete';

            divWrapper.append(removeFormButton);

            removeFormButton.addEventListener('click', (e) => {
                e.preventDefault();
                item.remove();
            });
        };

        const addFormToCollection = (e) => {
            const collectionHolder = document.querySelector('#' + e.currentTarget.dataset.collectionHolderId);

            const item = document.createElement('div');

            item.innerHTML = collectionHolder
                .dataset
                .prototype
                .replace(
                    /__name__/g,
                    collectionHolder.dataset.index
                );

            collectionHolder.appendChild(item);

            collectionHolder.dataset.index++;

            addPriceFormDeleteLink(item);
        };

        document
            .querySelectorAll('#admin-item-prices > div')
            .forEach((price) => {
                addPriceFormDeleteLink(price)
            });

        document
            .querySelectorAll('button.btn-add-price')
            .forEach(btn => {
                btn.addEventListener("click", addFormToCollection)
            });
    }
}

htmx.on('htmx:afterSwap', function (event) {
    refreshItems();
});

refreshItems();
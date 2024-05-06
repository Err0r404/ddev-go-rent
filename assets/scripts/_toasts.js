import * as bootstrap from "bootstrap";
import htmx from 'htmx.org';

var initToasts = function () {
    const toastList = document.querySelectorAll('.toast-container .toast');

    toastList.forEach(toast => {
        const toastInstance = new bootstrap.Toast(toast);
        toastInstance.show();
    });
}

htmx.on('htmx:afterSwap', function (event) {
    initToasts();
});

initToasts();
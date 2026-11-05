document.addEventListener('DOMContentLoaded', function () {
    const alertEl = document.querySelector('.alert-dismissible');
    if (alertEl) {
        const closeButton = alertEl.querySelector('.btn-close');
        closeButton?.addEventListener('click', function () {
            alertEl.remove();
        });
    }
});

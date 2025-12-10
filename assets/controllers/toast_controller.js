document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('animationend', function (e) {
        if (e.target.classList.contains('toast')) {
            e.target.remove();
        }
    }, true);
});
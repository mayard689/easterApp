
// eslint-disable-next-line no-undef
$(document).ready(() => {
    // eslint-disable-next-line no-undef
    $(window).keydown((event) => {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
        return true;
    });
});

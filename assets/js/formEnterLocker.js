
$(document).ready(() => {
    $(window).keydown((event) => {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
        return true;
    });
});

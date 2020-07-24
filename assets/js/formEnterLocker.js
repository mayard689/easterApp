
// eslint-disable-next-line no-undef
$(document).ready(() => {
    // eslint-disable-next-line no-undef
    $('#project-edit-form').keydown((event) => {
        if (event.keyCode === 13) {
            event.preventDefault();
            $('#project-save-button').click();
            return false;
        }
        return true;
    });
});

const buttons = document.querySelectorAll('.button_password');

// eslint-disable-next-line no-restricted-syntax
for (const button of buttons) {
    button.addEventListener('click', (e) => {
        const inputPassword = button.parentElement.parentElement.firstElementChild;
        const newInputType = inputPassword.getAttribute('type') === 'text' ? 'password' : 'text';

        button.firstChild.classList.toggle('fa-eye');
        button.firstChild.classList.toggle('fa-eye-slash');
        inputPassword.setAttribute('type', newInputType);
    });
}

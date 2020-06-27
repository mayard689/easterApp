const button = document.querySelectorAll('.button_password');

for (let i = 0; i < button.length; i++) {
    button[i].addEventListener('click', () => {
        const input = document.getElementsByClassName('form-control');
        const buttonIcon = document.getElementsByClassName('fas');

        if (buttonIcon[i].classList[1] === 'fa-eye') {
            buttonIcon[i].classList.remove('fa-eye');
            buttonIcon[i].classList.add('fa-eye-slash');
            input[i].setAttribute('type', 'text');
        } else {
            buttonIcon[i].classList.remove('fa-eye-slash');
            buttonIcon[i].classList.add('fa-eye');
            input[i].setAttribute('type', 'password');
        }
    });
}

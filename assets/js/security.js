const button = document.querySelectorAll('.button_password');

// eslint-disable-next-line no-plusplus
for (let i = 0; i < button.length; i++) {
    button[i].addEventListener('click', () => {
        let input = document.getElementsByClassName('form-control');
        const buttonIcon = document.getElementsByClassName('fas');

        if (buttonIcon[i].classList[1] === 'fa-eye') {
            buttonIcon[i].classList.remove('fa-eye');
            buttonIcon[i].classList.add('fa-eye-slash');

            if (document.getElementById('inputPassword') != null) {
                input = document.getElementById('inputPassword');
                input.setAttribute('type', 'text');
            } else {
                input[i].setAttribute('type', 'text');
            }
        } else {
            buttonIcon[i].classList.remove('fa-eye-slash');
            buttonIcon[i].classList.add('fa-eye');

            if (document.getElementById('inputPassword') != null) {
                input = document.getElementById('inputPassword');
                input.setAttribute('type', 'password');
            } else {
                input[i].setAttribute('type', 'password');
            }
        }
    });
}

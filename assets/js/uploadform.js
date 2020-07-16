const customFile = document.querySelectorAll('.custom-file');

// eslint-disable-next-line no-restricted-syntax
for (const item of customFile) {
    item.addEventListener('change', (e) => {
        const fileName = item.firstElementChild.files[0].name;
        const label = item.lastElementChild;

        label.innerHTML = fileName;
    });
}

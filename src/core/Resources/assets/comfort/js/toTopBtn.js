function executeFunc(func) {
    if (typeof func !== 'function') {
        return false;
    }

    func();
    return true;
}


function toTopButton() {
    const toTopBtn = document.getElementById('to-top');

    toTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', () => {
        if (window.scrollY > 200) {
        toTopBtn.style.display = 'block';
        } else {
        toTopBtn.style.display = 'none';
        }
    })
}


executeFunc(toTopButton);
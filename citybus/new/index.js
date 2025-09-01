
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.hero-image .slide');
    const dots = document.querySelectorAll('.hero-image .dot');
    const nextBtn = document.querySelector('.hero-image .next');
    const prevBtn = document.querySelector('.hero-image .prev');

    let index = 0;
    let timer;

    function showSlide(i) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        slides[i].classList.add('active');
        dots[i].classList.add('active');
        index = i;
    }

    function nextSlide() {
        showSlide((index + 1) % slides.length);
    }

    function prevSlide() {
        showSlide((index - 1 + slides.length) % slides.length);
    }

    nextBtn.addEventListener('click', () => {
        nextSlide();
        resetTimer();
    });

    prevBtn.addEventListener('click', () => {
        prevSlide();
        resetTimer();
    });

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            const i = parseInt(dot.getAttribute('data-index'));
            showSlide(i);
            resetTimer();
        });
    });

    function startTimer() {
        timer = setInterval(nextSlide, 3000);
    }

    function resetTimer() {
        clearInterval(timer);
        startTimer();
    }

    showSlide(index);
    startTimer();
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.cs-wbk-hero-slider').forEach(function (slider) {
    var slides = Array.prototype.slice.call(slider.querySelectorAll('.cs-wbk-hero-slide'));
    if (slides.length <= 1) {
      return;
    }

    var timer = parseInt(slider.getAttribute('data-timer') || '4500', 10);
    var current = 0;
    var intervalId;

    var setActiveSlide = function (index) {
      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden', 'true');

      slides[index].classList.add('is-active');
      slides[index].setAttribute('aria-hidden', 'false');

      current = index;
    };

    var goToNext = function () {
      setActiveSlide((current + 1) % slides.length);
    };

    var goToPrevious = function () {
      setActiveSlide((current - 1 + slides.length) % slides.length);
    };

    var restartAutoplay = function () {
      if (intervalId) {
        window.clearInterval(intervalId);
      }

      intervalId = window.setInterval(goToNext, Math.max(timer, 2000));
    };

    slides.forEach(function (slide, index) {
      slide.classList.toggle('is-active', index === 0);
      slide.setAttribute('aria-hidden', index === 0 ? 'false' : 'true');
    });

    slider.querySelectorAll('.cs-wbk-hero-slider-arrow').forEach(function (button) {
      button.addEventListener('click', function () {
        if (button.getAttribute('data-direction') === 'prev') {
          goToPrevious();
        } else {
          goToNext();
        }

        restartAutoplay();
      });
    });

    restartAutoplay();
  });
});

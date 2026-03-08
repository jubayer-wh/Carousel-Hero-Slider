document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.cs-wbk-hero-slider').forEach(function (slider) {
    var slides = slider.querySelectorAll('.cs-wbk-hero-slide');
    if (slides.length <= 1) {
      return;
    }

    var speed = parseInt(slider.getAttribute('data-speed') || '3500', 10);
    var current = 0;

    window.setInterval(function () {
      slides[current].classList.remove('is-active');
      current = (current + 1) % slides.length;
      slides[current].classList.add('is-active');
    }, Math.max(speed, 1500));
  });
});

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.cs-wbk-hero-slider').forEach(function (slider) {
    var slides = Array.prototype.slice.call(slider.querySelectorAll('.cs-wbk-hero-slide'));
    if (slides.length <= 1) {
      return;
    }

    var speed = parseInt(slider.getAttribute('data-speed') || '4500', 10);
    var current = 0;

    slides.forEach(function (slide, index) {
      slide.classList.toggle('is-active', index === 0);
      slide.setAttribute('aria-hidden', index === 0 ? 'false' : 'true');
    });

    window.setInterval(function () {
      var next = (current + 1) % slides.length;

      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden', 'true');

      slides[next].classList.add('is-active');
      slides[next].setAttribute('aria-hidden', 'false');

      current = next;
    }, Math.max(speed, 2000));
  });
});

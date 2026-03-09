document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-cs-settings-layout]').forEach(function (layout) {
    var buttons = Array.prototype.slice.call(layout.querySelectorAll('.cs-settings-nav__button'));
    var panels = Array.prototype.slice.call(layout.querySelectorAll('.cs-settings-panel'));

    var activatePanel = function (target, focusPanel) {
      buttons.forEach(function (button) {
        var isActive = button.getAttribute('data-panel-target') === target;
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      panels.forEach(function (panel) {
        var isActive = panel.id === target + '-panel';
        panel.classList.toggle('is-active', isActive);

        if (isActive) {
          panel.removeAttribute('hidden');
          if (focusPanel) {
            panel.focus();
          }
        } else {
          panel.setAttribute('hidden', 'hidden');
        }
      });
    };

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        activatePanel(button.getAttribute('data-panel-target'), false);
      });

      button.addEventListener('keydown', function (event) {
        var currentIndex = buttons.indexOf(button);
        var nextIndex = currentIndex;

        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
          nextIndex = (currentIndex + 1) % buttons.length;
        } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
          nextIndex = (currentIndex - 1 + buttons.length) % buttons.length;
        } else {
          return;
        }

        event.preventDefault();
        buttons[nextIndex].focus();
        activatePanel(buttons[nextIndex].getAttribute('data-panel-target'), true);
      });
    });
  });


  document.querySelectorAll('[data-cs-shortcode-copy]').forEach(function (copyWrap) {
    var copyButton = copyWrap.querySelector('[data-cs-shortcode-button]');
    var shortcodeEl = copyWrap.querySelector('[data-cs-shortcode-text]');

    if (!copyButton || !shortcodeEl) {
      return;
    }

    var defaultLabel = copyButton.getAttribute('data-copy-label') || 'Copy';
    var copiedLabel = copyButton.getAttribute('data-copied-label') || 'Copied!';

    copyButton.addEventListener('click', function () {
      var shortcode = shortcodeEl.textContent || '';

      if (!shortcode) {
        return;
      }

      var showCopiedState = function () {
        copyWrap.classList.remove('is-copied');
        void copyWrap.offsetWidth;
        copyWrap.classList.add('is-copied');

        copyButton.textContent = copiedLabel;

        window.setTimeout(function () {
          copyButton.textContent = defaultLabel;
          copyWrap.classList.remove('is-copied');
        }, 1200);
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(shortcode).then(showCopiedState).catch(function () {
          window.prompt('Copy shortcode:', shortcode);
        });

        return;
      }

      window.prompt('Copy shortcode:', shortcode);
    });
  });

});

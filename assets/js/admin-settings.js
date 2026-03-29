document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-cs-settings-layout]').forEach(function (layout) {
    var buttons = Array.prototype.slice.call(layout.querySelectorAll('.cs-settings-nav__button'));
    var panels = Array.prototype.slice.call(layout.querySelectorAll('.cs-settings-panel'));
    var form = layout.closest('form');
    var storageKey = 'wkhs_active_settings_tab';

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

      try {
        window.localStorage.setItem(storageKey, target);
      } catch (error) {
        // Ignore storage errors in restricted environments.
      }
    };

    if (buttons.length > 0) {
      try {
        var rememberedTab = window.localStorage.getItem(storageKey);

        if (rememberedTab && buttons.some(function (button) { return button.getAttribute('data-panel-target') === rememberedTab; })) {
          activatePanel(rememberedTab, false);
        }
      } catch (error) {
        // Ignore storage errors in restricted environments.
      }
    }

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

    if (form) {
      form.addEventListener('submit', function () {
        var activeButton = layout.querySelector('.cs-settings-nav__button.is-active');
        var saveFeedback = layout.querySelector('[data-cs-save-feedback]');

        if (activeButton) {
          try {
            window.localStorage.setItem(storageKey, activeButton.getAttribute('data-panel-target') || '');
          } catch (error) {
            // Ignore storage errors.
          }
        }

        if (saveFeedback) {
          saveFeedback.classList.remove('is-visible');
        }
      });
    }
  });

  document.querySelectorAll('[data-cs-shortcode-copy]').forEach(function (copyWrap) {
    var copyButton = copyWrap.querySelector('[data-cs-shortcode-button]');
    var shortcodeEl = copyWrap.querySelector('[data-cs-shortcode-text]');
    var tooltipEl = copyWrap.querySelector('[data-cs-shortcode-tooltip]');

    if (!copyButton || !shortcodeEl) {
      return;
    }

    var defaultLabel = copyButton.getAttribute('data-copy-label') || 'Copy';
    var copiedLabel = copyButton.getAttribute('data-copied-label') || 'Copied!';

    var showCopiedState = function () {
      copyWrap.classList.remove('is-copied');
      void copyWrap.offsetWidth;
      copyWrap.classList.add('is-copied');

      copyButton.textContent = copiedLabel;

      if (tooltipEl) {
        tooltipEl.setAttribute('aria-hidden', 'false');
      }

      window.setTimeout(function () {
        copyButton.textContent = defaultLabel;
        copyWrap.classList.remove('is-copied');

        if (tooltipEl) {
          tooltipEl.setAttribute('aria-hidden', 'true');
        }
      }, 1200);
    };

    copyButton.addEventListener('click', function () {
      var shortcode = shortcodeEl.textContent || '';

      if (!shortcode) {
        return;
      }

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

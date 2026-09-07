(function ($) {
  'use strict';

  function formatPrice(amount) {
    return 'Rs. ' + Math.round(amount).toLocaleString();
  }

  function showAuthMessage($form, message, isError) {
    var $msg = $form.find('.facefood-auth__message');
    $msg.text(message);
    $msg.toggleClass('is-error', !!isError);
    $msg.toggleClass('is-success', !isError);
  }

  function openPopup($popup) {
    $popup.removeAttr('hidden').attr('aria-hidden', 'false').addClass('is-open');
    $('body').addClass('facefood-popup-open');
  }

  function closePopup($popup) {
    $popup.attr('hidden', true).attr('aria-hidden', 'true').removeClass('is-open');
    $('body').removeClass('facefood-popup-open');
  }

  function initAuthPopups() {
    $('.facefood-popup').each(function () {
      var $popup = $(this);
      var forceOpen = $popup.data('force-open') === 1 || $popup.data('force-open') === '1';
      var autoShow = $popup.data('auto-show') === 1 || $popup.data('auto-show') === '1';
      var delay = parseInt($popup.data('delay'), 10) || 0;
      var showOnce = $popup.data('show-once') === 1 || $popup.data('show-once') === '1';
      var popupId = $popup.attr('id') || 'facefood-popup';
      var storageKey = 'facefood_popup_seen_' + popupId;

      if (forceOpen) {
        openPopup($popup);
        return;
      }

      if (showOnce && window.sessionStorage.getItem(storageKey) === '1') {
        return;
      }

      if (autoShow) {
        window.setTimeout(function () {
          openPopup($popup);
          if (showOnce) {
            window.sessionStorage.setItem(storageKey, '1');
          }
        }, delay * 1000);
      }
    });
  }

  function switchPopupTab($popup, tab) {
    $popup.find('.facefood-popup__tab').removeClass('is-active');
    $popup.find('.facefood-popup__panel').removeClass('is-active');
    $popup.find('.facefood-popup__tab[data-tab="' + tab + '"]').addClass('is-active');
    $popup.find('.facefood-popup__panel[data-panel="' + tab + '"]').addClass('is-active');
  }

  $(document).ready(initAuthPopups);

  $(document).on('click', '.facefood-popup-trigger', function () {
    var target = $(this).data('target');
    var tab = $(this).data('open-tab');
    var $popup = $('#' + target);
    if ($popup.length) {
      if (tab) {
        switchPopupTab($popup, tab);
      }
      openPopup($popup);
    }
  });

  $(document).on('click', '.facefood-popup__close, .facefood-popup__overlay', function () {
    closePopup($(this).closest('.facefood-popup'));
  });

  $(document).on('keydown', function (event) {
    if (event.key === 'Escape') {
      $('.facefood-popup.is-open').each(function () {
        closePopup($(this));
      });
    }
  });

  $(document).on('click', '.facefood-popup__tab', function () {
    var $tab = $(this);
    var tab = $tab.data('tab');
    var $popup = $tab.closest('.facefood-popup');

    $popup.find('.facefood-popup__tab').removeClass('is-active');
    $popup.find('.facefood-popup__panel').removeClass('is-active');
    $tab.addClass('is-active');
    $popup.find('.facefood-popup__panel[data-panel="' + tab + '"]').addClass('is-active');
  });

  $(document).on('click', '.facefood-tab', function () {
    var $tab = $(this);
    var target = $tab.data('target');
    var $wrap = $tab.closest('.facefood-category-menu');

    $wrap.find('.facefood-tab').removeClass('is-active');
    $wrap.find('.facefood-tab-panel').removeClass('is-active');

    $tab.addClass('is-active');
    $wrap.find('#' + target).addClass('is-active');
  });

  $(document).on('change', '.facefood-extra-toggle', function () {
    var $input = $(this);
    var $block = $input.closest('.facefood-toppings');
    var base = parseFloat($block.find('.facefood-total-price').data('base')) || 0;
    var total = base;

    $block.find('.facefood-extra-toggle:checked').each(function () {
      total += parseFloat($(this).data('extra-price')) || 0;
    });

    $block.find('.facefood-total-price').text(formatPrice(total));
  });

  $(document).on('submit', '.facefood-auth-form', function (event) {
    event.preventDefault();

    var $form = $(this);
    var action = $form.data('action');
    var $btn = $form.find('[type="submit"]');
    var payload = {
      action: 'facefood_' + action,
      nonce: facefoodPublic.nonce
    };

    $form.serializeArray().forEach(function (field) {
      payload[field.name] = field.value;
    });

    $btn.prop('disabled', true);
    showAuthMessage($form, 'Please wait...', false);

    $.post(facefoodPublic.ajaxUrl, payload)
      .done(function (response) {
        if (response.success) {
          showAuthMessage($form, response.data.message || 'Success', false);
          if (response.data.redirect) {
            window.location.href = response.data.redirect;
          } else {
            window.location.reload();
          }
          return;
        }

        showAuthMessage($form, (response.data && response.data.message) || 'Request failed.', true);
      })
      .fail(function (xhr) {
        var message = 'Request failed.';
        if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
          message = xhr.responseJSON.data.message;
        }
        showAuthMessage($form, message, true);
      })
      .always(function () {
        $btn.prop('disabled', false);
      });
  });

  $(document).on('click', '.facefood-logout-btn', function () {
    var $btn = $(this);
    $btn.prop('disabled', true);

    $.post(facefoodPublic.ajaxUrl, {
      action: 'facefood_logout',
      nonce: facefoodPublic.nonce
    }).always(function () {
      window.location.reload();
    });
  });
})(jQuery);

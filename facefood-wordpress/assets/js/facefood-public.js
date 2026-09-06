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

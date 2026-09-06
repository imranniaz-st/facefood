(function ($) {
  'use strict';

  $(document).on('click', '.facefood-tab', function () {
    var $tab = $(this);
    var target = $tab.data('target');
    var $wrap = $tab.closest('.facefood-category-menu');

    $wrap.find('.facefood-tab').removeClass('is-active');
    $wrap.find('.facefood-tab-panel').removeClass('is-active');

    $tab.addClass('is-active');
    $wrap.find('#' + target).addClass('is-active');
  });
})(jQuery);

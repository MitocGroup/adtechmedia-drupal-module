;(function ($) {
  $(function () {
    atmTpl.default.config({revenueMethod: 'micropayments'});
    var atmTemplating = atmTpl.default;

    atmTemplating.render('pledge', '#render-pledge-expanded').small(false);
    atmTemplating.render('pledge', '#render-pledge-collapsed');

    atmTemplating.render('pay', '#render-pay-expanded').small(false);
    atmTemplating.render('pay', '#render-pay-collapsed');

    atmTemplating.render('refund', '#render-refund-expanded').small(false);
    atmTemplating.render('refund', '#render-refund-collapsed');

  });
})(jQuery);

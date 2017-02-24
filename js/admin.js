;(function ($, D, W) {


  D.AjaxCommands.prototype.redirectInNewTab = function(ajax, response, status){
    if (status == 'success') {
      W.open(response.url, '_target');
    }
  };


  window.atmCloseModal = function () {
    $('#atm-terms-modal').hide();
  }

  $(function () {

    function updateComponent(componentName) {
      var options = {};
      var styles = {};

      $('.options-component-' + componentName).each(function (index, element) {
        var $element = $(element);
        var optionName = $element.data('option-name');
        options[optionName] = $element.val();
        styles[optionName] = {};
      });

      $('.styles-component-' + componentName).each(function (index, element) {
        var $element = $(element);
        var styleName = $element.data('style-name');
        var styleValue = $element.val();
        var $options = $($element.data('option-name').split(' '));

        $options.each(function (i, optionName) {
          styles[optionName][styleName] = styleValue;
        });
      });

      //console.log(componentName + 'Component', options, styles)

      atmTemplating.updateTemplate(componentName + 'Component', options, styles);

      for (var comp in atmTemplates) {
        if (typeof atmTemplates[comp].expanded != 'undefined') {
          atmTemplates[comp].expanded.redraw();
          atmTemplates[comp].collapsed.redraw();
        }
      }

      var output = atmTemplating.templateRendition(componentName + 'Component').render(options, styles);

      var $template = $(".templates-" + componentName);

      $template.val(
        JSON.stringify(output)
      );
      $template.removeAttr("disabled");
    }

    atmTpl.default.config({revenueMethod: 'micropayments'});
    var atmTemplating = atmTpl.default;

    var atmTemplates = {
      "pledge": {
        "expanded": atmTemplating.render('pledge', '#render-pledge-expanded'),
        "collapsed": atmTemplating.render('pledge', '#render-pledge-collapsed')
      },
      "pay": {
        "expanded": atmTemplating.render('pay', '#render-pay-expanded'),
        "collapsed": atmTemplating.render('pay', '#render-pay-collapsed')
      },
      "refund": {
        "expanded": atmTemplating.render('refund', '#render-refund-expanded'),
        "collapsed": atmTemplating.render('refund', '#render-refund-collapsed')
      },
      "auth": {

      }
    };

    for (var comp in atmTemplates) {
      if (typeof atmTemplates[comp].expanded != 'undefined') {
        atmTemplates[comp].expanded.small(false);
      }

      updateComponent(comp);
    }

    $('.js-component-options, .js-component-styles, .js-sync-values').on('change keyup', function() {
      var $this = $(this);

      if ($this.hasClass('js-sync-values')) {
        var classSync = $this.data('class-sync');
        $('.' + classSync).not(this).val($this.val());
      }

      var componentName = $(this).data('component-name');
      //console.log(componentName);
      updateComponent(componentName);
    });

    var $body = $('body');
    var modal =
      '<div id="atm-terms-modal" class="atm-modal">' +
        '<div class="atm-modal-content">' +
          '<span class="atm-close" onclick="atmCloseModal()">×</span>' +
          '<h1 class="atm-modal-header">Terms of Use</h1>' +
          '<div id="atm-modal-content">' +
            '<iframe src="https://www.adtechmedia.io/terms/dialog.html" frameborder="0"></iframe>' +
          '</div>' +
        '</div>' +
      '</div>'
    ;
    $body.append(modal);

    $('#atm-terms').on('click', function (event) {
      event.preventDefault();
      $('#atm-terms-modal').show();
    });


    $('.accordion-details').find('details').on('click', function (event) {
      if (event.target.nodeName === 'SUMMARY') {
        $(this).siblings().removeAttr('open');
      }
    });

  });
})(jQuery, Drupal, window);

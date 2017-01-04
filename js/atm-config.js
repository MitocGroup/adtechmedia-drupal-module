(function ($, Drupal) {

  var templates = [
    {
      name : 'pledge',
      component : 'pledgeComponent',
      dataTab : 'pledge',
      collapsed : '#render-pledge-collapsed',
      expanded : '#render-pledge-expanded',
      sections : [
        {
          dataTab : 'salutation',
          options : [{
            name : 'body-welcome',
            inputName : 'welcome',
            type : 'expanded'
          }]
        }, {
          dataTab : 'message',
          options : [{
            name : 'body-msg-mp',
            inputName : 'message-expanded',
            type : 'expanded'
          }, {
            name : 'heading-headline',
            inputName : 'message-collapsed',
            type : 'collapsed'
          }]
        }
      ]
    },
    {
      name : 'pay',
      component : 'payComponent',
      dataTab : 'pay',
      collapsed : '#render-pay-collapsed',
      expanded : '#render-pay-expanded',
      sections : [
        {
          dataTab : 'salutation',
          options : [{
            name : 'body-salutation',
            inputName : 'salutation',
            type : 'expanded'
          }]
        }, {
          dataTab : 'message',
          options : [{
            name : 'body-msg-mp',
            inputName : 'message-expanded',
            type : 'expanded'
          }, {
            name : 'heading-headline-setup',
            inputName : 'message-collapsed',
            type : 'collapsed'
          }]
        }
      ]
    },
    {
      name : 'refund',
      component : 'refundComponent',
      dataTab : 'refund',
      collapsed : '#render-refund-collapsed',
      expanded : '#render-refund-expanded',
      sections : [
        {
          dataTab : 'mood-ok',
          options : [{
            name : 'body-feeling-ok',
            inputName : 'body-feeling-ok',
            type : 'expanded'
          }]
        }, {
          dataTab : 'mood',
          options : [{
            name : 'body-feeling',
            inputName : 'body-feeling',
            type : 'expanded'
          }]
        }, {
          dataTab : 'mood-happy',
          options : [{
            name : 'body-feeling-happy',
            inputName : 'body-feeling-happy',
            type : 'expanded'
          }]
        }, {
          dataTab : 'mood-not-happy',
          options : [{
            name : 'body-feeling-not-happy',
            inputName : 'body-feeling-not-happy',
            type : 'expanded'
          }]
        }, {
          dataTab : 'message',
          options : [{
            name : 'body-msg',
            inputName : 'message-expanded',
            type : 'expanded'
          }, {
            name : 'heading-headline',
            inputName : 'message-collapsed',
            type : 'collapsed'
          }]
        }, {
          dataTab : 'share',
          options : [{
            name : 'body-share-experience',
            inputName : 'body-share-experience',
            type : 'expanded'
          }]
        }
      ]
    }
  ];


  function getCSSFields(inputs) {
    var styles = {};

    jQuery.each(inputs, function (i, input) {
      if (jQuery(input).val() !== '') {
        styles[jQuery(input).data('template-css')] = jQuery(input).val();
      }
    });
    return styles;
  }

  function getInputsData(inputs){
    var styles = {};
    jQuery.each(inputs, function (i, input) {
      if (jQuery(input).val() !== '') {
        if (jQuery(input).is(':checkbox')) {
          styles[jQuery(input).attr('name')] = jQuery(input).prop('checked');
        } else {
          styles[jQuery(input).attr('name')] = jQuery(input).val();
        }

      }
    });
    return styles;
  }

  function getPositionFields() {
    var inputs = jQuery('[data-template="position"] input');

    return getInputsData(inputs);
  }

  function getOverallStylingFields() {
    var styles = {},
      inputs = jQuery('[data-template="overall-styling"] input');
    jQuery.each(inputs, function (i, input) {
      if (jQuery(input).val() !== '') {
        styles[jQuery(input).attr('data-template-css')] = jQuery(input).val();
      }
    });
    return styles;
  }

  function getOverallStyling() {
    var css = '',
      stylesData = getOverallStylingFields();
    if (stylesData.hasOwnProperty('background-color')) {
      css += '.atm-base-modal {background-color: ' + stylesData['background-color'] + ';}' +
        '.atm-targeted-modal .atm-head-modal ' +
        '.atm-modal-heading {background-color: ' + stylesData['background-color'] + ';}';
    }
    jQuery.each(['border', 'box-shadow'], function (i, key) {
      if (stylesData.hasOwnProperty(key)) {
        css += '.atm-targeted-modal{'+key+': ' + stylesData[key] + ';}';
      }
    });
    if (stylesData.hasOwnProperty('footer-background-color')) {
      css += '.atm-base-modal .atm-footer{background-color: ' + stylesData['footer-background-color'] + ';}';
    }
    if (stylesData.hasOwnProperty('footer-border')) {
      css += '.atm-base-modal .atm-footer{border: ' + stylesData['footer-border'] + ';}';
    }
    if (stylesData.hasOwnProperty('font-family')) {
      css += '.atm-targeted-container .mood-block-info,' +
        '.atm-targeted-modal,' +
        '.atm-targeted-modal .atm-head-modal .atm-modal-body p,' +
        '.atm-unlock-line .unlock-btn {font-family: ' + stylesData['font-family'] + ';}';
    }
    return css;
  }
  function applayOverallStyling(css) {
    var style = jQuery('#overall-template-styling');
    style.html(css);
  }
  function fillOverallStylesFields(templateOverallStylesInputs) {
    /*global templateOverallStylesInputs*/
    var inputs = jQuery('[data-template="overall-styling"] input');
    jQuery.each(inputs, function (i, input) {
      var key = jQuery(input).attr('data-template-css');
      if (templateOverallStylesInputs.hasOwnProperty(key)) {
        jQuery(input).val(templateOverallStylesInputs[key])
      }
    });
  }
  function fillPositionFields(templatePositionInputs) {
    /*global templatePositionInputs*/
    var inputs = jQuery('[data-template="position"] input');
    jQuery.each(inputs, function (i, input) {
      var key = jQuery(input).attr('name');
      if (templatePositionInputs.hasOwnProperty(key)) {
        if (jQuery(input).is(':checkbox')) {
          //styles[jQuery(input).attr('name')] = jQuery(input).prop('checked');
          jQuery(input).prop('checked', templatePositionInputs[key]);
        } else {
          jQuery(input).val(templatePositionInputs[key])
        }
      }
    });

    if (!jQuery('#edit-sticky').prop('checked')) {
      jQuery('.disable-if-sticky input').attr('disabled', 'disabled');
    } else {
      jQuery('.disable-if-sticky input').removeAttr('disabled');
    }

    //templatePositionInputs.hasOwnProperty('sticky')

  }
  function fillCSSFields(key, inputValues, inputFields) {
    if (inputValues.hasOwnProperty(key)) {
      jQuery.each(inputValues[key], function (name, value) {
        inputFields[key].inputs.filter('[data-template-css="' + name + '"]').val(value);
      });
    }
  }
  function inputsToObject(inputs) {
    var res = {};
    jQuery.each(inputs, function (key, value) {
      res[key] = value.input.val();
    });
    return res;
  }
  function styleInputsToObject(inputs) {
    var res = {};
    jQuery.each(inputs, function (key, value) {
      res[key] = getCSSFields(value.inputs);
    });
    return res;
  }
  function getDatatemplate(value) {
    return '[data-template="' + value + '"]';
  }

  function toggleTemplates() {
    var sender = $($(this.$el).parents('[data-view]')[0]),
      viewKey = sender.attr('data-view-key'),
      type = sender.attr('data-view'),
      typeOther = 'expanded',
      small = true,
      senderParent = sender.parent(),
      senderParentExpaned = senderParent.find('[data-view-text="expanded"]'),
      senderParentCollapsed = senderParent.find('[data-view-text="collapsed"]');
    if (type === 'expanded') {
      typeOther = 'collapsed';
      small = false;
    }
    senderParent.find('[data-view="' + typeOther + '"]').attr('data-view', type);
    sender.attr('data-view', typeOther);
    views[viewKey][typeOther]._watchers['showModalBody'].forEach(unwatch => unwatch());
    delete views[viewKey][typeOther]._watchers['showModalBody'];
    views[viewKey][typeOther].small(small);
    views[viewKey][typeOther].watch('showModalBody', toggleTemplates);
    var tmp = views[viewKey]['expanded'];
    views[viewKey]['expanded'] = views[viewKey]['collapsed'];
    views[viewKey]['collapsed'] = tmp;

    tmp = senderParentExpaned.html();
    senderParentExpaned.html(senderParentCollapsed.html());
    senderParentCollapsed.html(tmp);
  }


  //atmTemplating.updateTemplate(
  //  views[viewKey].component,
  //  options[views[viewKey].component],
  //  styling[views[viewKey].component]
  //);
  //
  //// redraw the view
  //views[viewKey].expanded.redraw();
  //views[viewKey].collapsed.redraw();
  //views[viewKey].expanded.watch('showModalBody', toggleTemplates);
  //views[viewKey].collapsed.watch('showModalBody', toggleTemplates);


  Drupal.behaviors.atmConfig = {
    attach: function (context, settings) {



      // @todo from drupal config.
      var templateInputs = JSON.parse('{"pledgesalutationexpanded":"Dear XXX {user},8","pledgemessageexpanded":"!!Please TEST support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?14","pledgemessagecollapsed":"Please support quality journalism. {pledge-button}15","paysalutationexpanded":"Dear {user},41","paymessageexpanded":"Please support quality journalism. Would you pledge to pay a small fee of {price} to continue reading?47","paymessagecollapsed":"Support quality journalism. {pay-button} 48","refundmood-okexpanded":"Ok95","refundmoodexpanded":"How do you feel now?88","refundmood-happyexpanded":"Happy94","refundmood-not-happyexpanded":"Not happy96","refundmessageexpanded":"Thanks for contributing {price} and help us do the job we {heart}81","refundmessagecollapsed":"Premium content unlocked. notSatisfied_url Get immediate82","refundshareexpanded":"Share your experience97"}');
      var templateStyleInputs = JSON.parse('{"pledgesalutationstyle":{"color":"#000003","font-size":"9","font-weight":"10","font-style":"11","text-align":"12","text-transform":"13"},"pledgemessagestyle":{"color":"#000004","font-size":"16","font-weight":"17","font-style":"18","text-align":"19","text-transform":"20"},"paysalutationstyle":{"color":"#000010","font-size":"42","font-weight":"43"},"paymessagestyle":{"color":"#000011","font-size":"49","font-weight":"50","font-style":"51","text-align":"52","text-transform":"53"},"refundmood-okstyle":{"color":"#000022"},"refundmoodstyle":{"color":"#000020","font-size":"89","font-weight":"90","font-style":"91","text-align":"92","text-transform":"93"},"refundmood-happystyle":{"color":"#000021"},"refundmood-not-happystyle":{"color":"#000023"},"refundmessagestyle":{"color":"#000019","font-size":"83","font-weight":"84","font-style":"85","text-align":"86","text-transform":"87"},"refundsharestyle":{"color":"#000024","font-size":"98","font-weight":"99","font-style":"101","text-align":"102","text-transform":"103"}}');
      var templatePositionInputs =JSON.parse('{"sticky":true,"width":"600px","offset_top":"20px","offset_left":"-60px","scrolling_offset_top":"100px"}');
      var templateOverallStylesInputs =JSON.parse('{"background-color":"#ffffff","border":"solid 1px #eee","font-family":"Merriweather","box-shadow":"0 1px 2px 0 rgba(0, 0, 0, 0.1)","footer-background-color":"#f2f2f2","footer-border":"1px solid #eee"}');

      fillPositionFields(templatePositionInputs);
      fillOverallStylesFields(templateOverallStylesInputs);

      var atmTemplating = atmTpl.default;
      var stories = atmTemplating.stories();
      console.log('atmTemplating', atmTemplating);
      console.log('stories', stories);

      var views = {};
      var inputs = {};
      var options = {};
      var styling = {};
      var styleInputs = {};


      $.each(templates, function (i, template) {
        var tab = $(getDatatemplate(template.dataTab));
        options[template.component] = {};
        styling[template.component] = {};
        var viewKey = template.dataTab;
        views[viewKey] = {};
        $.each(template.sections, function (j, section) {
          var sectionTab = tab.find(getDatatemplate(section.dataTab));
          var styleInputsKey = viewKey + section.dataTab + 'style';
          styleInputs[styleInputsKey] = {
            inputs : sectionTab.find(getDatatemplate('style') + ' input ')
          };
          $.each(section.options, function (j, option) {
            var inputsKey = viewKey + section.dataTab + option.type;
            inputs[inputsKey] = {
              input : sectionTab.find('input[name="' + option.inputName + '"]'),
              optionName : option.name,
              type : option.type
            };
            if (templateInputs.hasOwnProperty(inputsKey)) {
              inputs[inputsKey].input.val(templateInputs[inputsKey]);
              options[template.component][option.name] = templateInputs[inputsKey];
              styling[template.component][option.name] = templateStyleInputs[styleInputsKey];
            }

          });
          fillCSSFields(styleInputsKey, templateStyleInputs, styleInputs);
        });

        views[viewKey]['expanded'] = atmTemplating.render(template.name, template.expanded);
        views[viewKey]['expanded'].small(false);
        views[viewKey]['component'] = template.component;
        views[viewKey]['collapsed'] = atmTemplating.render(template.name, template.collapsed);

        $(template.expanded).attr('data-view-key', viewKey);
        $(template.collapsed).attr('data-view-key', viewKey);

        console.log('component', template.component);
        console.log('options', options[template.component]);
        console.log('styling', styling[template.component]);

        //atmTemplating.updateTemplate(template.component, options[template.component], styling[template.component]);

        views[viewKey].expanded.redraw();
        views[viewKey].collapsed.redraw();
        views[viewKey].expanded.watch('showModalBody', toggleTemplates);
        views[viewKey].collapsed.watch('showModalBody', toggleTemplates);
      });

      /* var throttledSync = $.throttle(200, function (e) {
        var viewKey = $($(this).parents('[data-template]')[2]).data('template');

        var inputKey = viewKey + $($(this).parents('[data-template]')[1]).data('template')

        $.each(['expanded', 'collapsed'], function (i, type) {
          //console.log(type);
          if (inputs.hasOwnProperty(inputKey + type)) {
            options[views[viewKey].component][inputs[inputKey + type].optionName] = inputs[inputKey + type].input.val();
            styling[views[viewKey].component][inputs[inputKey + type].optionName] =
              getCSSFields(styleInputs[inputKey + 'style'].inputs);
          }
        });
        // update template

        atmTemplating.updateTemplate(
          views[viewKey].component,
          options[views[viewKey].component],
          styling[views[viewKey].component]
        );

        // redraw the view
        views[viewKey].expanded.redraw();
        views[viewKey].collapsed.redraw();
        views[viewKey].expanded.watch('showModalBody', toggleTemplates);
        views[viewKey].collapsed.watch('showModalBody', toggleTemplates);
      }); */

      var $form = $('section.views-tabs');
      var $inputs = $form.find('input');
      var $colorInputs = $form.find('input[type="color"]');
      //$inputs.bind('keyup', throttledSync);
      //$colorInputs.bind('change', throttledSync);

      // var overallSync = $.throttle(200, function () {
      //   applayOverallStyling(getOverallStyling());
      // });
      //$('[data-template="overall-styling"] input').bind('keyup', overallSync);
      //$('[data-template="overall-styling"] input[type="color"]').bind('change', overallSync);



      // Save template settings.
      $('.save-templates', context).bind('click', function (e) {
        e.preventDefault();
        var viewKey = $(this, context).attr('data-submit');

        $.ajax({
          type: 'POST',
          url: '/admin/config/system/adtechmedia/update-tpls',
          dataType: 'json',
          data: {
            component: viewKey,
            template: atmTemplating.templateRendition(views[viewKey].component).render(
              options[views[viewKey].component],
              styling[views[viewKey].component]
            ),
            styles: JSON.stringify(templateOverallStylesInputs)
            //styles: JSON.stringify(styleInputsToObject(styleInputs))
          },
          success: function (matches) {
            //if (typeof matches.status == 'undefined' || matches.status != 0) {
            //  db.cache[searchString] = matches;
            //  // Verify if these are still the matches the user wants to see.
            //  if (db.searchString == searchString) {
            //    db.owner.found(matches);
            //  }
            //  db.owner.setStatus('found');
            //}
          },
          error: function (xmlhttp) {
            alert(Drupal.ajaxError(xmlhttp, db.uri));
          }
        });
      });

      // Tabs switch.
      //$('.templates-views .tab-content:first', context).show();
      $('.templates-views', context).each(function(index) {
        $(this).find('.tab-content:first').show();
      });
      $('.horizontal-tabs', context).change(function() {
        $('.tab-content', context).hide();
        $('.' + $(this).val(), context).show();
      });

    }
  };

})(jQuery, Drupal);

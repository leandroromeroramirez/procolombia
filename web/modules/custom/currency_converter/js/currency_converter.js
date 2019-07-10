(function ($, Drupal, drupalSettings) {

  'use strict';
  
  function currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default){
    if(focus == 'amount_from'){
      if($(currency_to).val() == currency_default){//USD-*
        var currency_conv = $(currency_from).val() + '_' + currency_default;
        $(amount_to).val( ($(amount_from).val() * currencies[currency_conv]).toFixed(2) );
      }
      if($(currency_from).val() == currency_default){//*-USD
        var currency_conv = $(currency_to).val() + '_' + currency_default;
        $(amount_to).val( ($(amount_from).val() / currencies[currency_conv]).toFixed(5) );
      }
    }
    if(focus == 'amount_to'){
      if($(currency_to).val() == currency_default){//*-USD
        var currency_conv = $(currency_from).val() + '_' + currency_default;
        $(amount_from).val( ($(amount_to).val() / currencies[currency_conv]).toFixed(5) );
      }
      if($(currency_from).val() == currency_default){//USD-*
        var currency_conv = $(currency_to).val() + '_' + currency_default;
        $(amount_from).val( ($(amount_to).val() * currencies[currency_conv]).toFixed(2) );
      }
    }
    
    disabled_currency_default(currency_from, currency_to, currency_default);
    return true;
  }

  function disabled_currency_default(currency_from, currency_to, currency_default){
    if($(currency_from).val() == currency_default){ $(currency_from).prop('disabled', true).trigger("chosen:updated"); $(currency_to).prop('disabled', false).trigger("chosen:updated"); }
    if($(currency_to).val() == currency_default){ $(currency_to).prop('disabled', true).trigger("chosen:updated"); $(currency_from).prop('disabled', false).trigger("chosen:updated"); }
  }

  Drupal.behaviors.currency_converter = {
    attach: function(context, settings) {
      var focus = '',
      currencies = drupalSettings.settings.currency,
      currency_default = 'COP',
      currency_from = "#edit-container-form-1-currency-converter-from",
      currency_to = "#edit-container-form-2-currency-converter-to",
      amount_from = "#edit-container-form-1-amount-from",
      amount_to = "#edit-container-form-2-amount-to",
      change_currency = '#edit-change-currency';

      $(currency_from).change(function() {
        if($(currency_from).val() == currency_default && $(currency_to).val() == currency_default){ $('#edit-currency-converter-labels').text("Seleccione una nueva divisa");}else{ $('#edit-currency-converter-labels').text(""); }
          currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default);
      });
      $(currency_to).change(function() {
        if($(currency_from).val() == currency_default && $(currency_to).val() == currency_default){ $('#edit-currency-converter-labels').text("Seleccione una nueva divisa"); }else{ $('#edit-currency-converter-labels').text(""); }
          currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default);
      });
      
      $(change_currency).click(function(){
        if($(currency_from, context).length && $(currency_to, context).length){
          if($(currency_from).val() == currency_default && $(currency_to).val() == currency_default){ $('#edit-currency-converter-labels').text("Seleccione una nueva divisa"); }else{ $('#edit-currency-converter-labels').text(""); }
          var currency_change_from = $(currency_from).val(), currency_change_to = $(currency_to).val();
          //var amount_change_from = $(amount_from).val(), amount_change_to = $(amount_to).val();

          $(currency_from, context).val(currency_change_to);
          $(currency_from, context).trigger("chosen:updated");
          $(currency_to, context).val(currency_change_from);
          $(currency_to, context).trigger("chosen:updated");
          //$(amount_from, context).val(amount_change_to);
          //$(amount_to, context).val(amount_change_from);

          currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default);
        }
      });

      $(amount_from).on('input',function(){
        focus = 'amount_from';
        currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default);
      });

      $(amount_to).on('input',function(){
        focus = 'amount_to';
        currency_converter_to(focus, currency_from, currency_to, amount_from, amount_to, currencies, currency_default);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);

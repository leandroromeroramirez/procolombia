<?php

namespace Drupal\currency_converter;

interface CurrencyConverterManagerInterface {

  public function countries();

  public function defaultValueCurrency();
  
  public function currencies();
}

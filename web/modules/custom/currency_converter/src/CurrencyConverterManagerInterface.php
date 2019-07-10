<?php

namespace Drupal\currency_converter;

/**
 * Interface CurrencyConverterManagerInterface.
 *
 * @package Drupal\currency_converter
 */
interface CurrencyConverterManagerInterface {

  /**
   * Converts the amount from one currency to another.
   *
   * @param int $amount
   *   The Amount to convert.
   * @param string $from
   *   The from currency.
   * @param string $to
   *   The to currency.
   *
   * @return int
   *   Returns the converted amount.
   */
  public function convertAmount($amount, $from, $to);

  /**
   * Returns a list of all available Currency Converter Countries.
   *
   * @return array
   *   Returns a list of all available Currency Converter Countries.
   */
  public function countries();

  public function defaultValueCurrency();
  
  public function currencies();
}

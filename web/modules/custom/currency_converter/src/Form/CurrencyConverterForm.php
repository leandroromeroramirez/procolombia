<?php

namespace Drupal\currency_converter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\currency_converter\CurrencyConverterManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Currency Converter form.
 */
class CurrencyConverterForm extends FormBase {

  /**
   * The currency Converter.
   *
   * @var \Drupal\currency_converter\CurrencyConverterManagerInterface
   */
  protected $CurrencyConveter;

  /**
   * Constructs a new CurrencyConverterForm.
   *
   * @param \Drupal\currency_converter\CurrencyConverterManagerInterface $currency_converter
   *   The Currency Converter Manager.
   */
  public function __construct(CurrencyConverterManagerInterface $currency_converter) {
    $this->CurrencyConveter = $currency_converter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('currency_converter.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'currency_converter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $moduleRoot = drupal_get_path('module', 'currency_converter');
    $form['#attached']['library'][] = $moduleRoot . '/js/currency_converter.js';
    $form['#attached']['drupalSettings']['settings']['currency'] = $this->CurrencyConveter->currencies();

    $form['container_form_1'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => '<div class="medium-5 small-12 columns">',
      '#suffix' => '</div>',
    ];
    $form['container_form_1']['currency_converter_from'] = array(
      '#type' => 'select',
      '#title' => t('Tengo esta divisa'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => $this->CurrencyConveter->defaultValueCurrency()['currency_converter_from'],
      '#attributes' => array('class' => array('gcc-select-list')),
      '#required' => TRUE,
      '#prefix' => '<div class="medium-12 columns">',
      '#suffix' => '</div>',
    );
    $form['container_form_1']['amount_from'] = array(
      '#type' => 'number',
      '#title' => $this->t('Importe'),
      '#maxlength' => 15,
      '#default_value' => '',
      '#required' => TRUE,
      '#prefix' => '<div class="medium-12 columns">',
      '#suffix' => '</div>',
    );
    $form['change_currency'] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => array('id' => 'edit-change-currency', 'src' => '/' . $moduleRoot . '/img/converter.png'),
      '#prefix' => '<div class="medium-2 columns hide-for-small-only arrows-change-currency">',
      '#suffix' => '</div>',
    ];
    $form['container_form_2'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => '<div class="medium-5 small-12 columns">',
      '#suffix' => '</div>',
    ];
    $form['container_form_2']['currency_converter_to'] = array(
      '#type' => 'select',
      '#title' => t('Divisa en'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => $this->CurrencyConveter->defaultValueCurrency()['currency_converter_to'],
      '#attributes' => array('class' => array('gcc-select-list')),
      '#required' => TRUE,
      '#prefix' => '<div class="medium-12 columns">',
      '#suffix' => '</div>',
    );
    $form['container_form_2']['amount_to'] = array(
      '#type' => 'number',
      '#title' => $this->t('Importe'),
      '#maxlength' => 15,
      '#default_value' => '',
      '#required' => TRUE,
      '#prefix' => '<div class="medium-12 columns">',
      '#suffix' => '</div>',
    );
    $form['currency_converter_labels'] = array(
      '#type' => 'item',
      '#markup' => t(''),
      '#prefix' => '<div class="medium-12 columns">',
      '#suffix' => '</div>',
    );
    return $form;
    /*$form['currency_converter_from'] = array(
      '#type' => 'select',
      '#title' => t('Select Your Currency From'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => '',
      '#attributes' => array('class' => array('gcc-select-list')),
      '#required' => TRUE,
    );
    $form['currency_converter_to'] = array(
      '#type' => 'select',
      '#title' => t('Select Your Currency To'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => '',
      '#attributes' => array('class' => array('gcc-select-list')),
      '#required' => TRUE,
    );
    $form['amount'] = array(
      '#type' => 'number',
      '#title' => $this->t('Your Amount'),
      '#min' => 0,
      '#required' => TRUE,
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Convert'),
    );
    $form['#validate'][] = '::validateCurency';
    return $form;*/
  }

  /**
   * Checks from currency is not equal to converted currency.
   */
  public function validateCurency(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('currency_converter_from') === $form_state->getValue('currency_converter_to')) {
      $form_state->setErrorByName('currency_converter_to', $this->t('Please select different currency both currency are same.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $from = $form_state->getValue('currency_converter_from');
    $to = $form_state->getValue('currency_converter_to');
    $amount = $form_state->getValue('amount');
    $result = $this->CurrencyConveter->convertAmount($amount, $from, $to);

    $arguments = array(
      '@value_from' => $from,
      '@value_to' => $to,
      '@value_amount' => $amount,
      '@result' => $result,
    );
    $output = $this->t('Your selected value is from @value_from to @value_to amount is @value_amount @value_from &amp; your converted value is @result @value_to', $arguments);
    drupal_set_message($output);
  }

}

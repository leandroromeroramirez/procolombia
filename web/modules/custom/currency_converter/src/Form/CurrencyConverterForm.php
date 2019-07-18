<?php

namespace Drupal\currency_converter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\currency_converter\CurrencyConverterManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CurrencyConverterForm extends FormBase {

  protected $CurrencyConveter;

  public function __construct(CurrencyConverterManagerInterface $currency_converter) {
    $this->CurrencyConveter = $currency_converter;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('currency_converter.manager')
    );
  }

  public function getFormId() {
    return 'currency_converter_form';
  }

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
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}

<?php

namespace Drupal\currency_converter\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\currency_converter\CurrencyConverterManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\currency_converter\Form\CurrencyConverterForm;

/**
 * Provides a configurable block with Currency Converter Plugin.
 *
 * @Block(
 *  id = "currency_converter_block",
 *  admin_label = @Translation("Currency Converter Block"),
 * )
 */
class CurrencyConverterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The currency Converter.
   *
   * @var \Drupal\currency_converter\CurrencyConverterManagerInterface
   */
  protected $CurrencyConveter;


  /**
   * The Form Builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a new CurrencyConverterBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\currency_converter\CurrencyConverterManagerInterface $currency_converter
   *   The Currency Converter Manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The Form Builder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrencyConverterManagerInterface $currency_converter, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->CurrencyConveter = $currency_converter;
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('currency_converter.manager'),
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();

    $form['currency_converter_from'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Default Currency From'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => $config['currency_converter_from'],
      '#required' => TRUE,
    );
    $form['currency_converter_to'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Default Currency To'),
      '#options' => $this->CurrencyConveter->countries(),
      '#default_value' => $config['currency_converter_to'],
      '#required' => TRUE,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    if ($form_state->getValue('currency_converter_from') === $form_state->getValue('currency_converter_to')) {
      $form_state->setErrorByName('currency_converter_to', $this->t('Please select different currency both currency are same.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('currency_converter_from', $form_state->getValue('currency_converter_from'));
    $this->setConfigurationValue('currency_converter_to', $form_state->getValue('currency_converter_to'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();
    $form = $this->formBuilder->getForm(CurrencyConverterForm::class);
    $form['currency_converter_from']['#default_value'] = $config['currency_converter_from'];
    $form['currency_converter_to']['#default_value'] = $config['currency_converter_to'];
    $render['block'] = $form;
    $render['block']['#attached']['library'] = ['currency_converter/drupal.currency_converter'];
    return $render;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'currency_converter_from' => '',
      'currency_converter_to' => '',
    ];
  }

}

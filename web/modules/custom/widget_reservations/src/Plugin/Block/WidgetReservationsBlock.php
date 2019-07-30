<?php

namespace Drupal\widget_reservations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\widget_reservations\WidgetReservationsManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\widget_reservations\Form\WidgetReservationsForm;

/**
 * Provides a configurable block with Widget Reservations Plugin.
 *
 * @Block(
 *  id = "widget_reservations_block",
 *  admin_label = @Translation("Widget Reservations Block"),
 * )
 */
class WidgetReservationsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $WidgetReservations;

  protected $formBuilder;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, WidgetReservationsManagerInterface $widget_reservations, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->WidgetReservations = $widget_reservations;
    $this->formBuilder = $form_builder;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('widget_reservations.manager'),
      $container->get('form_builder')
    );
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    $form['widget_reservations_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#default_value' => $config['widget_reservations_key'],
      '#required' => TRUE,
    );
    return $form;
  }

  public function blockValidate($form, FormStateInterface $form_state) {
    /*if ($form_state->getValue('widget_reservations_key') === $form_state->getValue('widget_reservations_to')) {
      $form_state->setErrorByName('widget_reservations_to', $this->t('Please select different Reservations both Reservations are same.'));
    }*/
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('widget_reservations_key', $form_state->getValue('widget_reservations_key'));
  }

  public function build() {
    $config = $this->getConfiguration();
    $form = $this->formBuilder->getForm(WidgetReservationsForm::class);
    $form['widget_reservations_key']['#default_value'] = $config['widget_reservations_key'];
    $render['block'] = $form;
    $render['block']['#attached']['library'] = ['widget_reservations/drupal.widget_reservations'];
    return $render;
  }

  public function defaultConfiguration() {
    return [
      'widget_reservations_key' => ''
    ];
  }
}

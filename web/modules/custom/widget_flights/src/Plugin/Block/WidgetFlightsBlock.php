<?php

namespace Drupal\widget_flights\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\widget_flights\WidgetFlightsManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\widget_flights\Form\WidgetFlightsForm;

/**
 * Provides a configurable block with Widget Flights Plugin.
 *
 * @Block(
 *  id = "widget_flights_block",
 *  admin_label = @Translation("Widget Flights Block"),
 * )
 */
class WidgetFlightsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $Flights;

  protected $formBuilder;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, WidgetFlightsManagerInterface $widget_flights, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->Flights = $widget_flights;
    $this->formBuilder = $form_builder;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('widget_flights.manager'),
      $container->get('form_builder')
    );
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();
    return $form;
  }

  public function build() {
   $form = $this->formBuilder->getForm(WidgetFlightsForm::class);
    $render['block'] = $form;
    $render['block']['#attached']['library'] = ['widget_flights/drupal.widget_flights'];
    return $render;
  }

}

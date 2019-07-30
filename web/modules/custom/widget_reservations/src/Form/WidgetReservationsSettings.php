<?php

namespace Drupal\widget_reservations\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\widget_reservations\Controller\ReservationsConnection;
use Drupal\Core\Form\drupal_set_message;
use Drupal\Core\Cache\CacheTagsInvalidator;

class WidgetReservationsSettings extends ConfigFormBase implements ContainerInjectionInterface {

  protected $ApiKeyViator = NULL;
  protected $cacheTag = NULL;

  public function __construct(ReservationsConnection $ApiKeyViator, CacheTagsInvalidator $cacheTag) {
    $this->ApiKeyViator = $ApiKeyViator;
    $this->cacheTag = $cacheTag;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('widget_reservations.fetch_data'),
      $container->get('cache_tags.invalidator')
    );
  }

  protected function getEditableConfigNames() {
    return ['widget_reservations.settings'];
  }

  public function getFormId() {
    return 'widget_reservations_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['settings_viator'] = [
      '#type' => 'details',
      '#title' => $this->t('Viator API'),
      '#open' => TRUE,
    ];
    $form['settings_viator']['status'] = [
      '#type' => 'label',
      '#title' => $this->t('Register the Endpoint and ApiKey to use the Viator API in the widgets.'),
    ];
    $form['settings_viator']['endpoint_viator'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#default_value' => $this->config('widget_reservations.settings')->get('endpoint_viator') ? $this->config('widget_reservations.settings')->get('endpoint_viator') : '',
      '#required' => TRUE,
    );
    $form['settings_viator']['apikey_viator'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#default_value' => $this->config('widget_reservations.settings')->get('apikey_viator') ? $this->config('widget_reservations.settings')->get('apikey_viator') : '',
      '#required' => TRUE,
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('widget_reservations.settings')->set('endpoint_viator', $form_state->getValue('endpoint_viator'))->save();
    $this->config('widget_reservations.settings')->set('apikey_viator', $form_state->getValue('apikey_viator'))->save();
  }

}

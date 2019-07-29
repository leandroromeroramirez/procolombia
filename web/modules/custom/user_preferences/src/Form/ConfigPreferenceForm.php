<?php

namespace Drupal\user_preferences\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user_preferences\UtilitiesServiceInterface;

/**
 * Class ConfigPreferenceForm.
 */
class ConfigPreferenceForm extends ConfigFormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\user_preferences\UtilitiesServiceInterface;
   */
  protected $utilitiesServices;

  /**
   * Constructs a new ConfigPreferenceForm object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
      EntityTypeManagerInterface $entity_type_manager,
      UtilitiesServiceInterface $utilities_service
    ) {
    parent::__construct($config_factory);
        $this->entityTypeManager = $entity_type_manager;
        $this->utilitiesServices = $utilities_service;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('user_preferences.utilities')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'user_preferences.configpreference',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_preference_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('user_preferences.configpreference');
    $vocabulary = $this->utilitiesServices->getVocabulary();

    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Seleccione los elementos disponibles para las preferencias de usuario'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    
    foreach ($vocabulary['data'] as $key => $value) {

      $form['names_fieldset'][$value['term']] = [
        '#type' => 'fieldset',
        '#title' => t('Seleccione los elementos que desea que esten disponibles de '.$value['name']),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      ];

      if (!empty($value['childs'])) {
        foreach ($value['childs'] as $k => $val) {
          $form['names_fieldset'][$value['term']]['isActive_'.$k] = [
            '#type' => 'checkbox',
            '#default_value' => $config->get($value['term'] . '-' . 'isActive_'.$k),
            '#title' => t($val),
          ];
        }
      } else {
        $form['names_fieldset'][$value['term']]['isActive_'. $value['term']] = [
          '#type' => 'checkbox',
          '#default_value' => $config->get($value['term'] . '-' . 'isActive_'.$value['term']),
          '#title' => t($value['name']),
        ];
      }
      
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('user_preferences.configpreference');
    $fieldset = $form_state->getValue('names_fieldset');

    foreach ($fieldset as $key => $value) {
      foreach ($value as $k => $val) {
        $name = $key . '-' . $k;
        $config->set($name,$val)->save();
      }
    }

    $form_state->setRedirect('system.admin_config');
  }

}

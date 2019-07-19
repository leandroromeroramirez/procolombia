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
    $val_tax_father = 0;

    
    $form['father_taxonomy'] = [
      '#type' => 'select',
      '#title' => $this->t('select taxonomy father'),
      '#description' => $this->t('Select the parent taxonomy, then save and you can select the fields to use'),
      '#options' => [0 => $this->t('Ninguno')] + $this->utilitiesServices->buildListTerm($val_tax_father, 'destinos'),
      '#default_value' => $config->get('father_taxonomy'),
    ];

    if($config->get('father_taxonomy') != 0) {
      $val_tax = (int) $config->get('father_taxonomy');
      $form['group_tax'] = [
        '#type' => 'fieldset',
        '#title' => t('available taxonomies'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $datafield = $this->utilitiesServices->buildListTerm($val_tax);

      if(!empty($datafield)&& is_array($datafield)){
        foreach ($datafield as $key => $value) {
          $data_key = $this->utilitiesServices->convertName($value).'_'.$key ;
          $form['group_tax'][$data_key] = [
            '#type' => 'checkbox',
            '#default_value' => $config->get($data_key),
            '#title' => t($value),
          ];

          // $form['group_tax'][$data_key . "txt"] = [
          //   '#type' => 'select',
          //   '#default_value' => $config->get($data_key."txt"),
          //   '#title' => t('Select the entity to relate to '.$value),
          //   '#options' => [0 => 'Select'] + $this->getListEntityA2(),
          //   '#states' => [
          //     'visible' => [
          //       ':input[name="'.$data_key . '"]' => [
          //         'checked' => TRUE,
          //       ],
          //     ],
          //     'required' => [
          //       ':input[name="' . $data_key . '"]' => [
          //         'checked' => TRUE,
          //       ],
          //     ],
          //   ],
          // ];
        }
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

    $this->config('user_preferences.configpreference')
      ->set('test', $form_state->getValue('test'))
      ->save();
  }

}

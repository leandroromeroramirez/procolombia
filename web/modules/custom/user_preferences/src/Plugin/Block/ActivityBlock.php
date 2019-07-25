<?php

namespace Drupal\user_preferences\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\user_preferences\UtilitiesServiceInterface;

/**
 * Provides a 'ActivityBlock' block.
 *
 * @Block(
 *  id = "activity_block",
 *  admin_label = @Translation("Activity block"),
 * )
 */
class ActivityBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Array With view mode options.
   *
   * @var array
   */
  protected $viewModeOptions;

  /**
   * @var \Drupal\user_preferences\UtilitiesServiceInterface;
   */
  protected $utilitiesServices;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    AccountProxy $current_user,
    EntityDisplayRepositoryInterface $entityDisplayRepository,
    UtilitiesServiceInterface $utilities_service) {
    $this->currentUser = $current_user;
    $this->viewModeOptions = $entityDisplayRepository->getViewModeOptions('node');
    $this->utilitiesServices = $utilities_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_display.repository'),
      $container->get('user_preferences.utilities')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'view_mode' => '',
      'limit' => 10,
          ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('View mode'),
      '#options' => $this->viewModeOptions,
      '#default_value' => $this->configuration['view_mode'],
    ];

    $form['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Limit'),
      '#description' => $this->t('Number of the contents to show'),
      '#default_value' => $this->configuration['limit'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['view_mode'] = $form_state->getValue('view_mode');
    $this->configuration['limit'] = $form_state->getValue('limit');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'activity_block';
    $build['#attached']['library'][] =  'user_preferences/activity.block';
    $build['#view_mode'] = $this->configuration['view_mode'];

    $uid = $this->currentUser->id();
    $destiny = '';
    $limit = $this->configuration['limit'];

    $resultados =  $this->utilitiesServices->getActivityXUserXDestinyXlimit($uid, $destiny, $limit);


    
    $build['#list_activity'] = $resultados;

    $build['activity_block_test']['#markup'] = '<p>' . $this->configuration['test'] . '</p>';

    return $build;
  }

}

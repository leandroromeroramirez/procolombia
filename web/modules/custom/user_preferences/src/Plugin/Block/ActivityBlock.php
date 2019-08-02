<?php

namespace Drupal\user_preferences\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\user_preferences\UtilitiesServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\node\NodeInterface;

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
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;



  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxy $current_user,
    EntityDisplayRepositoryInterface $entityDisplayRepository,
    UtilitiesServiceInterface $utilities_service,
    CurrentRouteMatch $current_route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->viewModeOptions = $entityDisplayRepository->getViewModeOptions('node');
    $this->utilitiesServices = $utilities_service;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container, 
    array $configuration, 
    $plugin_id, 
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_display.repository'),
      $container->get('user_preferences.utilities'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      // 'view_mode' => '',
      // 'limit' => 10,
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
    $route_match = $this->currentRouteMatch->getRouteName();

    $uid = $this->currentUser->id();
    $destiny = '';
    $limit = $this->configuration['limit'];
    $render_controller = \Drupal::entityTypeManager()->getViewBuilder('node');
    if ($route_match == 'entity.node.canonical') {
      $node = $this->currentRouteMatch->getParameter('node');
      
      if ($node instanceof \Drupal\node\NodeInterface) {
        $destiny = $node->get('field_termino_destino');
        $resultados =  $this->utilitiesServices->getActivityXUserXDestinyXlimit($uid, $destiny, $limit);
        if (!empty($resultados)) {
          foreach ($resultados as $node) {
            $build['#list_activity'][$node->id()] = $render_controller->view($node, $this->configuration['view_mode']);
          }
        }
      }
    } elseif ($route_match == 'view.frontpage.page_1') {
        $resultados =  $this->utilitiesServices->getActivityXUserXDestinyXlimit($uid, $destiny, $limit);
        if (!empty($resultados)) {
          foreach ($resultados as $node) {
            $build['#list_activity'][$node->id()] = $render_controller->view($node, $this->configuration['view_mode']);
          }
        }
    }
    return $build;
  }

}

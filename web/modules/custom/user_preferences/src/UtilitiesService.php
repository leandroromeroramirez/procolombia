<?php

namespace Drupal\user_preferences;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Class UtilitiesService.
 */
class UtilitiesService implements UtilitiesServiceInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Constructs a new UtilitiesService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function buildListTerm($idTermFather, $idSection){
    $response = [];
    $tree = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree($idSection, $idTermFather, 1, TRUE);
    foreach ($tree  as $key => $value) {
      $response[$value->id()] =  $value->getName();
    }
    return $response;
  }

  public function convertName($name){
    $result = \Drupal::service('pathauto.alias_cleaner')->cleanString($name);
    return $result;
  }



}

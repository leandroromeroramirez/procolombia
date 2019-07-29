<?php

namespace Drupal\user_preferences;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;


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
   * Symfony\Component\DependencyInjection\ContainerAwareInterface definition.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
   */
  protected $entityQuery;

  /**
   * Constructs a new UtilitiesService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ContainerAwareInterface $entity_query) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityQuery = $entity_query;
  }

  public function buildListTerm($idSection, $idTermFather){
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

  public function getVocabulary() {
    $vocabulary = [];
    $val_tax_father = 0;

    $list_father= $this->buildListTerm('tipos_de_turismo', $val_tax_father);
    $vocabulary['name_machine'] = 'tipos_de_turismo';
    $vocabulary['name'] = 'Tipos de turismo';
    
    foreach ($list_father as $idTerm => $name) {
      $list_child = $this->buildListTerm('tipos_de_turismo', $idTerm);

      $vocabulary['data'][] = [
        'term' => $idTerm,
        'name' => $name,
        'childs' => $list_child
      ];
    }
    return $vocabulary;
  }

  public function getActivePreference() {
    $response = [];
    $vocabulary = $this->getVocabulary();
    $config = \Drupal::config('user_preferences.configpreference');

    foreach ($vocabulary['data'] as $key => $value) {
      //Validando el padre


      //VAlidando los hijos
      foreach ($value['childs'] as $k => $name) {
        $data = $config->get($value['term'] . '-' . 'isActive_'.$k);
        if ($data == 1) {
          $response[$value['term']]['childrens'][] = [
            'idTerm' => $k,
            'name' => $name,
          ];
        }
      }
      $response[$value['term']]['name'][] = $value['name'];
      $response[$value['term']]['term'][] = $value['term'];
    }
    return $response;
  }

  public function getActivityXUserXDestinyXlimit($uid, $destiny, $limit){
    $response = [];
    $node = $this->entityQuery->get('node');
    $list_user = $this->getListPrefererUser($uid);
    //kint($list_user);
    $id_node = $node->condition('status', 1)
    ->condition('type', 'actividad')
    ->execute();

    if(!empty($id_node)){
      $nodes = Node::loadMultiple($id_node);
      foreach ($nodes as $key => $node) {
        $destino = $node->get('field_destinos_relacionados');
        $val = reset($destino->getValue());
        $des = reset($destiny->getValue());
        if ($des['target_id'] == $val['target_id']) {
          $response[] = $node;
        }
      }

      if(!empty($response)){
       
      }

    }
    
    kint($response);
    // Cargar el usuario para extraer los datos de carga
    //$user = \Drupal\user\Entity\User::load($uid);

    //Aqui debe ir la consulta a el tipo de contenido Actividad

    // debe retornar las actividades 
  }

  public function getListPrefererUser($uid){
    $response = [];
    $userData = User::load($uid);
    if ($userData) {
      $data = $userData->get('field_preferencias');
      kint($data);
    }
    

  }

}

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
    $list_nodes = [];
    $id_node = $node->condition('status', 1)
    ->condition('type', 'actividad')
    ->execute();

    $count = 0;

    if(!empty($id_node)){
      $nodes = Node::loadMultiple($id_node);
      foreach ($nodes as $key => $node) {
        $destinoNode = $node->get('field_destinos_relacionados');

        $val = reset($destinoNode->getValue());
        if (!empty($destiny)) {
          $des = reset($destiny->getValue());
        } else {
          if ($count <= $limit) {
            $response[] = $node->id();
            $count++;
          } else {
            break;
          }
        }
       

        if ($des['target_id'] == $val['target_id']) {
          $list_nodes[] = $node->id();
        }
      }

      if(!empty($list_nodes)){
       foreach ($list_nodes as $nd) {
          $list_turismo = $nd->get('field_tipos_de_turismo')->getValue();
          $flag = false;
          
          if (!empty($list_user)) {
            foreach ($list_user as $k => $v) {
              foreach ($list_turismo as $k2 => $v2) {
                if ($v['target_id'] == $v2['target_id']) {
                  $flag = true;
                  break;
                }
                
              }
              if ($flag) {
                break;
              }
            }
            if ($flag) {
              if ($count <= $limit) {
                $response[] = $nd->id();
                $count++;
              } else {
                break;
              }
            }
          } else {
            if ($count <= $limit) {
              $response[] = $nd->id();
              $count++;
            } else {
              break;
            }
          }
       }
      } else {
        $cont = 0;
        foreach ($nodes as $id => $node) {
          if ($cont > $limit) {
            break;
          } else {
            $response[] = $node->id();
            $cont++;
          }
        }
      }
    }
    return $response;
  }

  public function getListPrefererUser($uid){
    $response = [];
    $userData = $this->entityTypeManager->getStorage('user')->load($uid);
    if ($userData) {
      $data = $userData->get('field_preferencias');
      if (!empty($data->getValue())) {
        $response = $data->getValue();
      }
    }
    return $response;
  }

}

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

  public function getVocabulary() {
    $vocabulary = [];
    $val_tax_father = 0;
    $vocabulary[0]['term'] = $this->buildListTerm($val_tax_father, 'destinos');
    $vocabulary[0]['name_machine'] = 'destinos';
    $vocabulary[0]['name'] = 'Destinos';

    $vocabulary[1]['term'] = $this->buildListTerm($val_tax_father, 'tipos_de_actividad');
    $vocabulary[1]['name_machine'] = 'tipos_de_actividad';
    $vocabulary[1]['name'] = 'Tipos de actividad';

    $vocabulary[2]['term'] = $this->buildListTerm($val_tax_father, 'tipos_de_turismo');
    $vocabulary[2]['name_machine'] = 'tipos_de_turismo';
    $vocabulary[2]['name'] = 'Tipos de turismo';

    $vocabulary[3]['term'] = $this->buildListTerm($val_tax_father, 'recomendaciones');
    $vocabulary[3]['name_machine'] = 'recomendaciones';
    $vocabulary[3]['name'] = 'Recomendaciones';

    return $vocabulary;
  }

  public function getActivePreference() {
    $response = [];
    $vocabulary = $this->getVocabulary();
    $config = \Drupal::config('user_preferences.configpreference');

    foreach ($vocabulary as $key => $value) {
      foreach ($value['term'] as $k => $val) {
        $data = $config->get($value['name_machine'] . '-' . 'isActive_'.$k);
        if ($data == 1) {
          $response[$key]['idTerm'][] = $k;
          
        }
      }
      $response[$key]['name_machine'][] = $value['name_machine'];
      $response[$key]['name'][] = $value['name'];
    }
    return $response;
  }

  public function getActivityXUserXDestinyXlimit($uid, $destiny, $limit){
    // Cargar el usuario para extraer los datos de carga
    //$user = \Drupal\user\Entity\User::load($uid);

    //Aqui debe ir la consulta a el tipo de contenido Actividad

    // debe retornar las actividades 
  }


}

<?php

namespace Drupal\widget_flights\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\widget_flights\WidgetFlightsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WidgetFlightsForm extends FormBase {

  protected $Flights;

  public function __construct(WidgetFlightsManagerInterface $widget_flights) {
    $this->Flights = $widget_flights;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('widget_flights.manager')
    );
  }

  public function getFormId() {
    return 'widget_flights_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $moduleRoot = drupal_get_path('module', 'widget_flights');
    
    if(isset($_SESSION) && !empty($_SESSION)){
      $data = $this->Flights->flights($_SESSION);
      if(isset($data) && !empty($data)){
        $form['#attached']['library'][] = $moduleRoot . '/js/widget_flights.js';
        $form['#attached']['drupalSettings']['settings']['flights_config'] = $data;

        $header = $data['header']; 
        $flights = $data['flights'];

        $form['container_flight'] = [
          '#type' => 'container',
          '#tree' => TRUE,
          '#prefix' => '<div class="row">',
          '#suffix' => '</div>',
        ];

        $form['container_flight']['svg'] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => array('id' => 'edit-svg'),
          '#prefix' => '<div class="medium-7 columns">',
          '#suffix' => '</div>',
        ];

        $form['container_flight']['container_flight_list'] = [
          '#type' => 'container',
          '#tree' => TRUE,
          '#prefix' => '<div class="medium-5 columns">',
          '#suffix' => '</div>',
        ];
        
        $html .= '<header><h3>'.t('AEROLÍNEAS QUE VUELAN A COLOMBIA DESDE '.$header['origin']['country']).'</h3></header>';
       
        foreach ($flights as $key => $value) {
          $html_item[] = '
          <div class="small-6 columns aerolinea end">
            <div class="views-field views-field-title">
              <span class="field-content">'.$value['airline'].'</span>
            </div>
            <div class="views-field views-field-body">
              <div>'.$value['origin'].' - '.$value['destination'].'</div>
            </div>
          </div>';
          if(count($html_item) == 8 || $key == count($flights)-1 ){
            $html_owl[] = '<div class="item">'.implode($html_item).'</div>';
            $html_item = [];
          }
        }
        
        $html .= '<div id="owl-flights">'.implode($html_owl).'</div>';

        $form['container_flight']['container_flight_list']['ddff'] = [
          '#type' => 'markup',
          '#markup' => $html,
        ];
        return $form;
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}

}

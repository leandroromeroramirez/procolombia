<?php

namespace Drupal\widget_flights;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class WidgetFlightsManager implements WidgetFlightsManagerInterface {

  use StringTranslationTrait;

  public function __construct(TranslationInterface $string_translation) {
    $this->stringTranslation = $string_translation;
  }

  public function flights($location) {

    if(isset($location) && !empty($location)){
      if(isset($location['_sf2_attributes']) && !empty($location['_sf2_attributes']) && isset($location['_sf2_attributes']['smart_ip']) && !empty($location['_sf2_attributes']['smart_ip'])){
        $continent_code = $location['_sf2_attributes']['smart_ip']['location']['originalData']['continent']['code'];
        $continent = $location['_sf2_attributes']['smart_ip']['location']['originalData']['continent']['names']['es'];
        $country_code = $location['_sf2_attributes']['smart_ip']['location']['originalData']['country']['iso_code'];
        $country = $location['_sf2_attributes']['smart_ip']['location']['originalData']['country']['names']['es'];
        if($continent == 'Sudamérica'){$continent = 'Suramérica';}
      }
    }
    
    $moduleRoot = drupal_get_path('module', 'widget_flights');

    $data = [];
    $csv = array_map('str_getcsv', file($moduleRoot . '/csv/aerolineas.csv'));

    foreach ($csv as $key => $value) {
      $file = str_replace(';', '', $value[key($value)]);
      if(!empty($file)){
        $flight = explode(';', $value[key($value)]);
        $arrayName = array(
          'continent_code' => $continent_code,
          'continent' => $flight[0],
          'country_code' => $country_code,
          'country' => $flight[1],
          'origin' => $flight[2],
          'destination' => $flight[3],
          'airline' => $flight[4],
        );
        $data[] = $arrayName;
      }
    }
    $flights = [];
    foreach ($data as $key => $value) {/*Buscar x País*/
      if(array_search($country,$data[$key],true) == 'country'){
        $flights[] = $value;
        $header = array('origin' => array('continent_code' => $value['continent_code'], 'country_code' => $value['country_code'], 'country' => $value['country']), 'destination' => 'CO');
      }
    }
    if(isset($flights) && empty($flights)){
      foreach ($data as $key => $value) {
        if(array_search($continent,$data[$key],true) == 'continent'){/*Buscar x Continente*/
          $flights[] = $value;
          $header = array('origin' => array('continent_code' => $value['continent_code'], 'country_code' => null, 'country' => $value['continent']), 'destination' => 'CO');
        }
      }
    }

    $svg = file_get_contents($moduleRoot . '/img/widget_flights.svg');

    return array('header' => $header, 'flights' => $flights, 'svg' => $svg);
  }
}

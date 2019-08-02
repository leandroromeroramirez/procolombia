<?php

namespace Drupal\widget_reservations\Controller;

use Drupal\Core\Database\Connection;
use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\Client;
use Drupal\Core\Url;

class ReservationsConnection {

  protected $connection;
  protected $config;
  protected $http;

  public function __construct(Connection $connection, ConfigFactoryInterface $config, Client $httpClient) {
    $this->connection = $connection;
    $this->config = $config;
    $this->http = $httpClient;
  }

  public function getTabs($form) {
    $parameters = ["destId" => 4560,"seoId" => null,"catId" => 18,"subCatId" => 0,"dealsOnly" => false,"currencyCode" => "USD","topX" => "1-6","sortOrder" => "TOP_SELLERS"];
    $tabs = [
      ['id'=> 'activity', 'name'=>'Actividades'],
      //['id'=> 'flight', 'name'=>'Vuelo'],
      //['id'=> 'hotel', 'name'=>'Hotel'],
      //['id'=> 'flight-hotel', 'name'=>'Vuelo+Hotel']
    ];

    foreach ($tabs as $key => $value) {
      $active = $key == 0 ? ' is-active' : '';
      $li .= '<li class="tabs-title'.$active.'"><a href="#panel'.$value['id'].'" aria-selected="true">'.$value['name'].'</a></li>';
    }

    $form['reservations_tabs'] = [
      '#type' => 'markup',
      '#markup' => isset($li) ? $li : '',
      '#prefix' => '<ul class="tabs" data-tabs id="reservations-tabs">',
      '#suffix' => '</ul>',
    ];
    
    foreach ($tabs as $key => $value) {
      $form = array_merge($form, $this->getTab($value['id'], 'create', $parameters));
    }
    return $form;
  }

  public function getTab($type, $event, $parameters) {
    switch ($type) {
      case 'activity':

        $form = $this->getForm($type);

        $data = $this->getData('post', 'search/products', $parameters);
        $data = !empty($data) ? json_decode($data)->data : [];
      
        if(!empty($data)){
          foreach ($data as $key => $value) {
            $div .= '<div id="widget_reservations_tab_activity" class="small-6 medium-3 columns end destino">
            <div class="photo">
            <a href="'.$value->webURL.'" arget="_blank"><img src="'.$value->thumbnailHiResURL.'" width="450" height="450" alt=""></a>
            </div>
            <h3 class="titulo-baldosa"><a href="'.$value->webURL.'" target="_blank">'.$value->title.'</a></h3>
            <div class="reserve-price">'.$value->priceFormatted.' '.$value->currencyCode.'</div>
            </div>';
          }
        }else{
           $div .= '<div id="widget_reservations_tab_activity" class="medium-12 columns"><div class="results">No results found</div></div>';
        }

        $form1 = '<div id="genrateGraph" class="medium-12 columns">'.$div.'</div>';
        $form['tabs_content']['tabs_panel']['content'] = [
            '#type' => 'container',
            '#tree' => TRUE,
            '#prefix' => '<div id="genrateGraph" class="medium-12 columns">'.$div,
            '#suffix' => '</div>',
        ];
      default:
        # code...
        break;
    }
    return ($event == 'create') ? $form : $form1;
  }

  public function getForm($type) {
    $form['tabs_content'] = [
        '#type' => 'container',
        '#tree' => FALSE,
        '#prefix' => '<div class="tabs-content">',
        '#suffix' => '</div>',
    ];

    switch ($type) {
      case 'activity':
        $data = $this->getData('get', 'taxonomy/categories');
        $data = !empty($data) ? json_decode($data)->data : [];
        foreach ($data as $key => $value) {
          $element[$value->id] = $value->groupName;
        }
        $form['tabs_content']['tabs_panel_activity'] = [
            '#type' => 'container',
            '#tree' => FALSE,
            '#prefix' => '<div class="tabs-panel is-active" id="panel'.$type.'">',
            '#suffix' => '</div>',
        ];
        $form['tabs_content']['tabs_panel_activity']['destination'] = [
          '#type' => 'textfield',
          '#title' => 'Destination',
          '#default_value' => 'Bogotá',
        ];
        $form['tabs_content']['tabs_panel_activity']['categories'] = [
          '#type' => 'select',
          '#title' => 'Categories',
          '#options' => $element,
          '#default_value' => '18',
        ];
        $form['tabs_content']['tabs_panel_activity']['submission'] = [
          '#type' => 'submit',
          '#value' => 'Reservations',
          '#ajax' => [
            'callback' => '::gettingData',
            'event' => 'click',
            'progress' => [
              'type' => 'throbber',
              'message' => 'Reservations wait',
            ],
          ],
        ];
        break;
      default:
        # code...
        break;
    }
    return $form;
  }

  public function getData($method, $service, $parameters = []) {
    $tripadvisor_id = $this->config->get('widget_reservations.settings')->get('apikey_viator');
    $request_url = 'http://viatorapi.viator.com/service/'.$service;

    $data = array(
      'apiKey' => $tripadvisor_id,
    );

    $request_url = Url::fromUri($request_url, $options = ['query' => $data])->toUriString();

    switch ($method) {
      case 'get':
        try {
          $result = null;
          
          $response = $this->http->get($request_url);
          if ($response->getStatusCode() == '200' && $data = $response->getBody(TRUE)->getContents()) {
            $result = $data;
            //\Drupal::cache()->set('tripadvisor_integration:' . $tripadvisor_id . ':' . $langcode, $tripadvisor_data, time() + //\Drupal::config('tripadvisor_integration.admin_settings')->get('tripadvisor_integration_cache_expiration', 3600));
          }
          return $result;
        }catch (Exception $e) {
          watchdog_exception('tripadvisor_integration', $e);
          drupal_set_message(t('Unable to retrieve data from API.'), 'error');
        }
        break;
      case 'post':
        try {
          $result = null;
 
          $response = $this->http->post($request_url, ['json' => $parameters]);
          if ($response->getStatusCode() == '200' && $data = $response->getBody(TRUE)->getContents()) {
            $result = $data;
            //\Drupal::cache()->set('tripadvisor_integration:' . $tripadvisor_id . ':' . $langcode, $tripadvisor_data, time() + //\Drupal::config('tripadvisor_integration.admin_settings')->get('tripadvisor_integration_cache_expiration', 3600));
          }
          return $result;
        }catch (Exception $e) {
          watchdog_exception('tripadvisor_integration', $e);
          drupal_set_message(t('Unable to retrieve data from API.'), 'error');
        }
        break;
    }
  }
}

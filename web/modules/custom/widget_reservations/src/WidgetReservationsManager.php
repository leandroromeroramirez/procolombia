<?php

namespace Drupal\widget_reservations;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

class WidgetReservationsManager implements WidgetReservationsManagerInterface {

  use StringTranslationTrait;

  public function __construct(TranslationInterface $string_translation) {
    $this->stringTranslation = $string_translation;
  }

  public function activities() {
  	return $this->getDataEndpoint();
  }

  public function getDataEndpoint() {
	$tripadvisor_id = '6429515970269567352';
	$request_url = 'http://viatorapi.viator.com/service/taxonomy/categories';

	$data = array(
		'apiKey' => $tripadvisor_id,
	);

    try {
		$request_url = Url::fromUri($request_url, $options = ['query' => $data])->toUriString();
		$response = \Drupal::httpClient()->get($request_url);
		if ($response->getStatusCode() == '200' && $data = $response->getBody(TRUE)->getContents()) {
			$tripadvisor_data = json_decode($data);
	
	        /*\Drupal::cache()->set('tripadvisor_integration:' . $tripadvisor_id . ':' . $langcode, $tripadvisor_data, time() + \Drupal::config('tripadvisor_integration.admin_settings')->get('tripadvisor_integration_cache_expiration', 3600));*/

		}
		else {
			$tripadvisor_data = FALSE;
		}
		return $tripadvisor_data;
	}
    catch (Exception $e) {
      watchdog_exception('tripadvisor_integration', $e);
      drupal_set_message(t('Unable to retrieve data from API.'), 'error');
    }
  }
}

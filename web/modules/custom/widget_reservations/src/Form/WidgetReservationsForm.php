<?php

namespace Drupal\widget_reservations\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\widget_reservations\WidgetReservationsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\widget_reservations\Controller\ReservationsConnection;


class WidgetReservationsForm extends FormBase implements ContainerInjectionInterface {

  protected $WidgetReservations;

  public function __construct(WidgetReservationsManagerInterface $widget_reservations, ReservationsConnection $RC) {
    $this->WidgetReservations = $widget_reservations;
    $this->RC = $RC;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('widget_reservations.manager'), $container->get('widget_reservations.fetch_data')
    );
  }

  public function getFormId() {
    return 'widget_reservations_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $html = $this->RC->getTabs($form);
    return $html;
  }

  public function gettingData(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $parameters = array(
      "destId" => 4560,//$form_state->getValue('destination')
      "seoId" => null,
      "catId" => $form_state->getValue('categories'),
      "subCatId" => 0,
      "dealsOnly" => false,
      "currencyCode" => "USD",
      "topX" => "1-6",
      "sortOrder" => "TOP_SELLERS"
    );
    //"startDate" => "2019-07-29",
    //"endDate" => "2019-07-31",

    $options = $this->RC->getTab('activity', 'change', $parameters);
    $response->addCommand(new HtmlCommand('#genrateGraph', $options));

    return $response;

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}

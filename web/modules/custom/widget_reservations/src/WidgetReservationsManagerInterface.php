<?php

namespace Drupal\widget_reservations;

interface WidgetReservationsManagerInterface {
  public function activities();

  public function getDataEndpoint();
}

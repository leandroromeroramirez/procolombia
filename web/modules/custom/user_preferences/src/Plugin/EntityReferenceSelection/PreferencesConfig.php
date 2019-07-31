<?php

namespace Drupal\user_preferences\Plugin\EntityReferenceSelection;

use Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection;

/**
 * Provides specific access control for the node entity type.
 *
 * @EntityReferenceSelection(
 *   id = "custom:preferences_config",
 *   label = @Translation("Program by default city"),
 *   entity_types = {"taxonomy_term"},
 *   group = "custom",
 *   weight = 3
 * )
 */
class PreferencesConfig extends TermSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $target_bundles = $this->configuration['target_bundles'];
    $query = parent::buildEntityQuery($match, $match_operator);

    

    if (!empty($target_bundles) && in_array('programs', $target_bundles)) {
      $config_trailer = \Drupal::config('lafm_programming.settings');
      if ($id = $config_trailer->get('city_default')) {
        $query->condition('field_city_program', $id, '=');
      }
    }

    return $query;
  }

}
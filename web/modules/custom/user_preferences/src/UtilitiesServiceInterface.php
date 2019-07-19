<?php

namespace Drupal\user_preferences;

/**
 * Interface UtilitiesServiceInterface.
 */
interface UtilitiesServiceInterface {
    public function buildListTerm($idTermFather, $idSection);
    public function convertName($name);

}

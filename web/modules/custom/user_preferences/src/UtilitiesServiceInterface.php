<?php

namespace Drupal\user_preferences;

/**
 * Interface UtilitiesServiceInterface.
 */
interface UtilitiesServiceInterface {
    public function buildListTerm($idTermFather, $idSection);
    public function convertName($name);
    public function getVocabulary();
    public function getActivePreference();
    public function getActivityXUserXDestinyXlimit($uid, $destiny, $limit);
}

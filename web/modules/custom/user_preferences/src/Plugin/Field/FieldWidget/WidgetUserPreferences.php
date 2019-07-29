<?php

namespace Drupal\user_preferences\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'widget_user_preferences' widget.
 *
 * @FieldWidget(
 *   id = "widget_user_preferences",
 *   label = @Translation("Widget user preferences"),
 *   field_types = {
 *     "field_user_preferences"
 *   }
 * )
 */
class WidgetUserPreferences extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $vocabulary = \Drupal::service('user_preferences.utilities')->getActivePreference();


    foreach ($vocabulary as $key => $value) {
      $element['names_fieldset'][$value['name_machine']] = $element +[
        '#type' => 'fieldset',
        '#title' => t('Vocabulario de '.$value['name']),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      ];

      // foreach ($value['term'] as $k => $val) {
      //   $element['names_fieldset'][$value['name_machine']]['isActive_'.$k] = [
      //     '#type' => 'checkbox',
      //     '#title' => t($val),
      //   ];
      // }
    }

    // $element['value'] = $element + [
    //   '#type' => 'textfield',
    //   '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
    //   '#size' => $this->getSetting('size'),
    //   '#placeholder' => $this->getSetting('placeholder'),
    //   '#maxlength' => $this->getFieldSetting('max_length'),
    // ];

    return $element;
  }

}

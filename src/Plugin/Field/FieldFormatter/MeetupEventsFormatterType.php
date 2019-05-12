<?php

namespace Drupal\meetup_events_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'meetup_events_formatter_type' formatter.
 *
 * @FieldFormatter(
 *   id = "meetup_events_formatter_type",
 *   label = @Translation("Meetup Upcoming Events"),
 *   field_types = {
 *     "meetup_events_field_type"
 *   }
 * )
 */
class MeetupEventsFormatterType extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    // Not to cache this field formatter.
    $elements['#cache']['max-age'] = 0;

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $settings = $item->getFieldDefinition()->getSettings();
    $api_key = $settings['meetup_api_key'];
    $client = \Drupal::httpClient();
    $request = $client->request('GET', 'https://api.meetup.com/2/events?key='. $api_key . '&group_urlname=' . $item->value . '&sign=true');
    $response = json_decode($request->getBody());
    $item_return_text = '';
    if (!empty($response->results)) {
      foreach ($response->results as $result) {
        $item_return_text .= '<h3>' . $result->name . '</h3>';
      }
    }
    else {
      $item_return_text = 'No upcoming events.';
    }

    return $item_return_text;
  }

}

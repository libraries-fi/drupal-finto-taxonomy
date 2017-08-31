<?php

namespace Drupal\finto_taxonomy\Element;

use Drupal;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete as BaseAutocomplete;
use Drupal\finto_taxonomy\TaxonomyHelper;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Site\Settings;


/**
 * @FormElement("finto_taxonomy_autocomplete")
 */
class EntityAutocomplete extends BaseAutocomplete {
  public function getInfo() {
    $info = parent::getInfo();
    $class = get_class($this);

    $process = [$class, 'processFintoAutocomplete'];
    $stack = &$info['#process'];

    foreach ($stack as $i => $callable) {
      if (is_a($callable[0], $class, true) && $callable[1] == 'processAutocomplete') {
        array_splice($stack, $i, 0, [$process]);
        break;
      }
    }

    return $info;
  }

  /**
   * Validates the autocomplete input.
   *
   * If new Finto terms are found in the input, the widget will create or update the corresponding
   * taxonomy term entries and then passes control to the parent implementation, which takes care
   * of actual validation.
   */
  public static function validateEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $cache = Drupal::service('cache.finto_taxonomy');
    $vocabulary = $element['#selection_settings']['finto_vocabulary'];
    $langcode = $form_state->getFormObject()->getFormLangcode($form_state);
    $storage = Drupal::entityTypeManager()->getStorage('taxonomy_term');

    if (is_array($element['#value'])) {
      $inputs = $element['#value'];
    } else {
      $inputs = $element['#tags'] ? Tags::explode($element['#value']) : [$element['#value']];
    }

    foreach ($inputs as $i => $input) {
      $finto_id = static::extractFintoIdFromAutocompleteInput($input);

      if ($finto_id) {
        $cache_id = TaxonomyHelper::termCacheId($finto_id, $vocabulary, $langcode);
        $item = $cache->get($cache_id, TRUE);

        if (!$item) {
          /*
           * This is a lazy way for handling cache mismatches. Should rather pull the data again
           * from the API. Expiration time is set to one hour but items can be lost when an admin
           * flushes the system caches.
           */
          $form_state->setError($element, t('Cached entry was not found.'));
          return;
        }

        $result = $storage->loadByProperties([
          'vid' => TaxonomyHelper::VOCABULARY_ID,
          'finto_url' => $item->data->uri
        ]);

        if ($result) {
          $term = reset($result);
          if (!$term->hasTranslation($langcode)) {
            $storage->createTranslation($term, $langcode, [
              'name' => $item->data->prefLabel,
            ]);
          }
        } else {
          $term = $storage->create([
            'vid' => TaxonomyHelper::VOCABULARY_ID,
            'name' => $item->data->prefLabel,
            'finto_url' => $item->data->uri,
            'finto_vid' => $item->data->vocab,
            'langcode' => $langcode,
          ]);
          $term->save();
        }

        $translation = $term->getTranslation($langcode);
        $inputs[$i] = sprintf('%s (%d)', $term->label(), $term->id());
      }
    }

    $element['#value'] = $element['#tags'] ? Tags::implode($inputs) : $inputs[0];

    return parent::validateEntityAutocomplete($element, $form_state, $complete_form);
  }

  public static function extractFintoIdFromAutocompleteInput($input) {
    if (preg_match('/.+\s\((p\d+)\)/', $input, $matches)) {
      return $matches[1];
    }
  }

  public static function processFintoAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element['#autocomplete_route_name'] = 'finto_taxonomy.entity_autocomplete';
    $element['#autocomplete_route_parameters']['finto_vocabulary'] = $element['#selection_settings']['finto_vocabulary'];

    // When strict mode forced, use custom matcher that allows matches against 'finto' vocabulary only.
    if (!empty($element['#selection_settings']['finto_autocomplete_strict'])) {
      $handler = 'finto_taxonomy_strict:taxonomy_term';

      // The controller checks this hash for security, so we need to re-calculate it.
      $data = serialize($element['#selection_settings']) . $element['#target_type'] . $handler;
      $selection_settings_key = Crypt::hmacBase64($data, Settings::getHashSalt());

      $element['#autocomplete_route_parameters']['selection_handler'] = $handler;
      $element['#autocomplete_route_parameters']['selection_settings_key'] = $selection_settings_key;

      $key_value_storage = \Drupal::keyValue('entity_autocomplete');
      if (!$key_value_storage->has($selection_settings_key)) {
        $key_value_storage->set($selection_settings_key, $element['#selection_settings']);
      }
    }

    return $element;
  }
}

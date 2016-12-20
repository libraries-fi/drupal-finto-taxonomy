<?php

namespace Drupal\finto_taxonomy\Element;

use Drupal;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete as BaseAutocomplete;
use Drupal\finto_taxonomy\TaxonomyHelper;

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
   * NOTE: This implementation allows only one value per input field. If the given value does not
   * contain a valid Finto term ID (e.g. 'p12345'), validation is passed to the parent class'
   * implementation.
   */
  public static function validateEntityAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $cache = Drupal::service('cache.finto_taxonomy');
    $vocabulary = $element['#selection_settings']['finto_vocabulary'];
    $langcode = $form_state->getFormObject()->getFormLangcode($form_state);
    $storage = Drupal::entityTypeManager()->getStorage('taxonomy_term');

    if ($element['#tags']) {
      $form_state->setError($element, t('This widget does not support tags-mode.'));
      return;
    }

    $finto_id = static::extractFintoIdFromAutocompleteInput($element['#value']);

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

      $result = $storage->loadByProperties(['finto_url' => $item->data->uri]);

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
      $element['#value'] = sprintf('%s (%d)', $term->label(), $term->id());
    }

    return parent::validateEntityAutocomplete($element, $form_state, $complete_form);
  }

  protected static function createTerm(array $values) {
    $storage = Drupal::entityTypeManager()->getStorage('taxonomy_term');

  }

  public static function extractFintoIdFromAutocompleteInput($input) {
    if (preg_match('/.+\s\((p\d+)\)/', $input, $matches)) {
      return $matches[1];
    }
  }

  public static function processFintoAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element['#autocomplete_route_name'] = 'finto_taxonomy.entity_autocomplete';
    $element['#autocomplete_route_parameters']['finto_vocabulary'] = $element['#selection_settings']['finto_vocabulary'];
    return $element;
  }
}

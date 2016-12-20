<?php

namespace Drupal\finto_taxonomy;

use Drupal\Core\Cache\CacheBackendInterface;

class TaxonomyHelper {
  const VOCABULARY_ID = 'finto';

  protected $cache;

  public static function termCacheId($term_id, $vocabulary, $langcode) {
    return sprintf('%s.%s.%s', $vocabulary, $term_id, $langcode);
  }

  /**
  * Checks whether an element is set to use the Finto autocomplete feature.
  *
  * Entities can be configured to employ Finto autocomplete by adding 'finto_autocomplete' => true
  * in the handler_settings array.
  */
  public static function elementAllowsFintoAutocomplete(array $element) {
    // The requirement is to have an entity_reference field that contains taxonomy_term entities.
    if (empty($element['#target_type']) || $element['#target_type'] != 'taxonomy_term') {
      return false;
    }

    // For now we only support extending the entity_autocomplete field.
    if ($element['#type'] != 'entity_autocomplete') {
      return false;
    }
    return !empty($element['#selection_settings']['finto_autocomplete']);
  }

  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * Helper function for caching the result items fetched from the Finto API.
   */
  public function setCachedFintoItems(array $items) {
    $cache = [];

    foreach ($items as $item) {
      $cid = static::termCacheId($item->localname, $item->vocab, $item->lang);
      $cache[$cid] = [
        'data' => $item,
        'expire' => time() + 3600,
      ];
    }

    if (!empty($cache)) {
      $this->cache->setMultiple($cache);
    }
  }

  /**
   * Helper function to complement the setter. Does nothing special in particular.
   */
  public function getCachedFintoItems(array $cids) {
    return $this->cache->getMultiple($cids, TRUE);
  }
}

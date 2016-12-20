<?php

namespace Drupal\finto_taxonomy\Controller;

use Drupal\system\Controller\EntityAutocompleteController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FintoAutocompleteController extends EntityAutocompleteController {
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('finto_taxonomy.autocomplete_matcher'),
      $container->get('keyvalue')->get('entity_autocomplete')
    );
  }
}

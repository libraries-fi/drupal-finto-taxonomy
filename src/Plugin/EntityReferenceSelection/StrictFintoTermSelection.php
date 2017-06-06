<?php

namespace Drupal\finto_taxonomy\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\Plugin\EntityReferenceSelection\TermSelection;

/**
 * This custom selection allows for restricting autocomplete to Finto terms.
 *
 * Only use this selection class for displaying suggestions per user input. It makes no sense
 * to make this the default completer for the field, because then additional vocabularies are always
 * ignored.
 *
 * @EntityReferenceSelection(
 *   id = "finto_taxonomy_strict:taxonomy_term",
 *   label = @Translation("Strict Finto selection"),
 *   entity_types = {"taxonomy_term"},
 *   group = "default",
 *   weight = 1
 * )
 */
class StrictFintoTermSelection extends TermSelection {
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityManagerInterface $entity_manager, ModuleHandlerInterface $module_handler, AccountInterface $current_user) {
    // Vocabulary always forced to 'finto'.
    $configuration['handler_settings']['target_bundles'] = ['finto' => 'finto'];

    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_manager, $module_handler, $current_user);
  }
}

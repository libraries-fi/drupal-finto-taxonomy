<?php

namespace Drupal\finto_taxonomy\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
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
 *   group = "finto_taxonomy_strict",
 *   weight = 1000
 * )
 */
class StrictFintoTermSelection extends TermSelection {
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, AccountInterface $current_user, EntityFieldManagerInterface $entity_field_manager = NULL, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, EntityRepositoryInterface $entity_repository = NULL) {
    // Vocabulary always forced to 'finto'.
    
    $configuration['target_bundles'] = ['finto' => 'finto'];
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $module_handler, $current_user, $entity_field_manager, $entity_type_bundle_info, $entity_repository);
  }

}

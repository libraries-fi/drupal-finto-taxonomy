Finto Taxonomy for Drupal 8
===========================

This module provides a simple integration of Finto dictionaries with Drupal 8's standard taxonomy. Current implementation supports a single Drupal taxonomy vocabulary and allows one to configure the used Finto dictionary per-element.

### Features
- Integrates querying Finto API straight into autocomplete widget.
- New terms are created automatically.
- Term translations are created automatically when they are used for the first time.
- No duplicates: display Finto word aliases but store terms only by their primary name.
- For each term also its source vocabulary and resource URI are stored.

### Configuration
Finto integration is enabled by setting two configuration options on the entity field that should be enabled to use Finto queries. Option **finto_autocomplete** enables the feature while option **finto_vocabulary** is used to configure which Finto vocabulary to use as the source. These configuration options have to be added under 'handler_settings' group.

```php
class MyEntity extends ContentEntityBase {
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $field['tags'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel('Tags')
      ->setSettings([
        'target_type' => 'taxonomy_term',
        'handler' => 'default',
        'handler_settings' => [
          'finto_autocomplete' => true,
          'finto_vocabulary' => 'yso',
          'target_bundles' => [
            // This is the taxonomy vocabulary provided by the module.
            'finto' => 'finto',
          ],
          'sort' => [
            'field' => 'name',
            'direction' => 'ASC',
          ],
        ],
      ]);
  }
}
```

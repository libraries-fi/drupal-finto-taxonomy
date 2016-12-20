<?php

namespace Drupal\finto_autocomplete\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteTagsWidget;

class FintoAutocomplete extends EntityReferenceAutocompleteWidget {

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

  }
}

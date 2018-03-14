<?php

namespace Drupal\finto_taxonomy\Plugin\LanguageNegotiation;

use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;
use Symfony\Component\HttpFoundation\Request;

/**
 * Overrides language parameter for our autocomplete route.
 *
 * NOTE: This affects only the autocomplete suggestions. If new terms are added by input, their
 * langcode will be set to whatever is the active language in the form submit handler. This is
 * term entities are created only when the form is submitted, which is separate from the autocomplete
 * route.
 */
class AutoCompleteRoute extends LanguageNegotiationUrl
{
    const METHOD_ID = 'finto-autocomplete-route';
    const QUERY_PARAMETER = 'langcode';

    const PATH_PREFIX = '/finto_taxonomy/taxonomy_term/finto_taxonomy_strict';

    public function getLangcode(Request $request = NULL) {
        if (strpos($request->getPathInfo(), self::PATH_PREFIX) === 0) {
            $langcode = $request->query->get(self::QUERY_PARAMETER);
            $language_enabled = array_key_exists($langcode, $this->languageManager->getLanguages());

            if ($language_enabled) {
                return $langcode;
            }
        }

        return parent::getLangcode();
    }
}

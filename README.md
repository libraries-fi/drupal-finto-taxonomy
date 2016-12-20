Finto Taxonomy for Drupal 8
===========================

This module provides integration of Finto dictionaries with Drupal 8's standard taxonomy. Current
implementation supports a single Drupal taxonomy vocabulary and allows one to configure the used
Finto dictionary per-element.

Features:
- Integrates querying Finto API straight into autocomplete widget (tags mode NOT supported).
- New terms are created automatically.
- Term translations are created automatically when they are used for the first time.
- No duplicates: display Finto word aliases but store terms only by their primary name.
- For each term also its source vocabulary and resource URI are stored.

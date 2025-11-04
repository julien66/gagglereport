/**
 * @file
 * Rules list builders search behavior.
 *
 * This code was forked from the core file:
 *   core/modules/views_ui/js/views_ui.listing.js
 * and is up-to-date as of core commit:
 *   8aa8ce1ffbcca9c727f46e58c714e1d351f7ef88 (9 Sept 2022)
 * Any changes to that core file after the above commit should be applied here
 * as well.
 */

(function ($, Drupal) {
  /**
   * Filters the rules list builder tables by a text input search string.
   *
   * Text search input: input.rules-filter-text
   * Target table:      input.rules-filter-text[data-table]
   * Source text:       [data-drupal-selector="rules-table-filter-text-source"]
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the filter functionality to the rules admin text search field.
   */
  Drupal.behaviors.rulesTableFilterByText = {
    attach(context, settings) {
      const [input] = once('rules-filter-text', 'input.rules-filter-text');
      if (!input) {
        return;
      }
      const $table = $(input.getAttribute('data-table'));
      let $rows;

      function filterViewList(e) {
        const query = e.target.value.toLowerCase();

        function showViewRow(index, row) {
          const sources = row.querySelectorAll(
            '[data-drupal-selector="rules-table-filter-text-source"]',
          );
          let sourcesConcat = '';
          sources.forEach((item) => {
            sourcesConcat += item.textContent;
          });
          const textMatch = sourcesConcat.toLowerCase().indexOf(query) !== -1;
          $(row).closest('tr').toggle(textMatch);
        }

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          $rows.each(showViewRow);
        } else {
          $rows.show();
        }
      }

      if ($table.length) {
        $rows = $table.find('tbody tr');
        $(input).on('keyup', filterViewList);
      }
    },
  };
})(jQuery, Drupal);

/**
 * @file
 * Js helper for Rate.
 *
 * After unvoting ratio button must be un-checked but it became impossible to
 * do with pure Drupal ajax.
 *
 * @todo: Related to https://www.drupal.org/project/drupal/issues/994360
 * so this js could be removed if fixed in core.
 */

(function ($, Drupal) {
  Drupal.behaviors.rateUnvoteHelper = {
    attach(context, settings) {
      $('table.rating-table', context).each(function () {
        const $this = $(this);
        const $ratesVotes = $this.find('.rate-voted');
        if ($ratesVotes.length === 0) {
          $this.find('input:radio').attr('checked', false);
        }
      });
    },
  };
})(jQuery, Drupal);

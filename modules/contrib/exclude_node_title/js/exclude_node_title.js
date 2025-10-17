/**
 * @file
 * JavaScript code for admin page
 */

(function ($) {
  function updateLabel(nodeType) {
    return function () {
      const excludeType = document.querySelector(
        `#edit-exclude-node-title-content-type-value-${nodeType}`,
      )?.value;

      if (excludeType !== 'none') {
        $(
          `label[for='edit-exclude-node-title-content-type-modes-${nodeType}']`,
        ).prop(
          'textContent',
          `Exclude title from ${
            excludeType === 'all' ? 'all ' : 'user defined'
          } nodes in the following view modes:`,
        );
      }
    };
  }

  Drupal.behaviors.excludeNodeTitle = {
    attach(context, settings) {
      Object.keys(settings.exclude_node_title.content_types).forEach((type) => {
        $(`#edit-exclude-node-title-content-type-value-${type}`).change(
          updateLabel(type),
        );
      });
    },
  };
})(jQuery);

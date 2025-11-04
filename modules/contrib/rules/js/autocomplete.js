/**
 * @file
 * Forked from core's autocomplete.
 *
 * Changed to:
 * - Immediately pop-up autocomplete suggestions when the field gets focus.
 * - Use rules-* classes instead of form-* classes.
 * - Start autocompletion with a minimum length of 0.
 */

(function ($, Drupal) {
  let autocomplete;

  /**
   * JQuery UI autocomplete source callback.
   *
   * @param {object} request
   *   The request object.
   * @param {function} response
   *   The function to call with the response.
   */
  function sourceData(request, response) {
    const elementId = this.element.attr('id');

    if (!(elementId in autocomplete.cache)) {
      autocomplete.cache[elementId] = {};
    }

    // Get the desired term and construct the autocomplete URL for it.
    const term = request.term;

    /**
     * Transforms the data object into an array and update autocomplete results.
     *
     * @param {object} data
     *   The data sent back from the server.
     */
    function sourceCallbackHandler(data) {
      autocomplete.cache[elementId][term] = data;

      response(data);
    }

    // Check if the term is already cached.
    if (autocomplete.cache[elementId].hasOwnProperty(term)) {
      response(autocomplete.cache[elementId][term]);
    } else {
      const options = $.extend(
        { success: sourceCallbackHandler, data: { q: term } },
        autocomplete.ajax,
      );
      $.ajax(this.element.attr('data-autocomplete-path'), options);
    }
  }

  /**
   * Handles an autocomplete focus event.
   *
   * @return {boolean}
   *   Always returns false.
   */
  function focusHandler() {
    return false;
  }

  /**
   * Handles an autocomplete select event.
   *
   * Restarts autocomplete process when the selection ends in a dot, for nested
   * data selectors.
   *
   * @param {jQuery.Event} event
   *   The event triggered.
   * @param {object} ui
   *   The jQuery UI settings object.
   *
   * @return {boolean}
   *   Returns false to indicate the event status.
   */
  function selectHandler(event, ui) {
    const inputValue = ui.item.value;
    if (inputValue.substr(inputValue.length - 1) === '.') {
      $(event.target).trigger('keydown');
    }
  }

  /**
   * Override jQuery UI _renderItem function to output HTML by default.
   *
   * @param {jQuery} ul
   *   jQuery collection of the ul element.
   * @param {object} item
   *   The list item to append.
   *
   * @return {jQuery}
   *   jQuery collection of the ul element.
   */
  function renderItem(ul, item) {
    return $('<li>').append($('<a>').html(item.label)).appendTo(ul);
  }

  /**
   * Attaches the autocomplete behavior to all required fields.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the autocomplete behaviors.
   * @prop {Drupal~behaviorDetach} detach
   *   Detaches the autocomplete behaviors.
   */
  Drupal.behaviors.autocomplete = {
    attach(context) {
      // Act on textfields with the "rules-autocomplete" class.
      const $autocomplete = $(
        once('autocomplete', 'input.rules-autocomplete', context),
      );

      if ($autocomplete.length) {
        var closing = false;

        $.extend(autocomplete.options, {
          close: function () {
            // Avoid double-pop-up issue.
            closing = true;
            setTimeout(function () {
              closing = false;
            }, 300);
          },
        });

        // Use jQuery UI Autocomplete on the textfield.
        $autocomplete.autocomplete(autocomplete.options).each(function () {
          $(this).data('ui-autocomplete')._renderItem =
            autocomplete.options.renderItem;

          // Immediately pop out the autocomplete when the field gets focus.
          $(this).focus(function () {
            if (!closing) {
              $(this).autocomplete('search');
            }
          });

          // Use CompositionEvent to handle IME inputs. It requests remote server
          // on "compositionend" event only.
          $autocomplete.on('compositionstart.autocomplete', () => {
            autocomplete.options.isComposing = true;
          });

          $autocomplete.on('compositionend.autocomplete', () => {
            autocomplete.options.isComposing = false;
          });
        });
      }
    },
    detach(context, settings, trigger) {
      if (trigger === 'unload') {
        $(
          once.remove('autocomplete', 'input.rules-autocomplete', context),
        ).autocomplete('destroy');
      }
    },
  };

  /**
   * Autocomplete object implementation.
   *
   * @namespace Drupal.autocomplete
   */
  autocomplete = {
    cache: {},

    /**
     * JQuery UI option object.
     *
     * @name Drupal.autocomplete.options
     */
    options: {
      source: sourceData,
      focus: focusHandler,
      select: selectHandler,
      renderItem: renderItem,
      minLength: 0,
      // Custom options, indicate IME usage status.
      isComposing: false,
    },
    ajax: {
      dataType: 'json',
      jsonp: false,
    },
  };
})(jQuery, Drupal);

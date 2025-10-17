/**
 * @file
 * Overriding core's message functions to add icon and a remove button to each message.
 */

(Drupal => {
  /**
   * Overrides message theme function.
   *
   * @param {object} message
   *   The message object.
   * @param {string} message.text
   *   The message text.
   * @param {object} options
   *   The message context.
   * @param {string} options.type
   *   The message type.
   * @param {string} options.id
   *   ID of the message, for reference.
   *
   * @return {HTMLElement}
   *   A DOM Node.
   */
  Drupal.theme.message = ({ text }, { type, id }) => {
    const messagesTypes = Drupal.Message.getMessageTypeLabels();
    const messageWrapper = document.createElement('div');
    console.log(text, type, id);
    messageWrapper.setAttribute('class', `mt-style-custom-all messages-list__item messages messages--${type}`);
    messageWrapper.setAttribute('data-drupal-selector', 'messages');
    messageWrapper.setAttribute('role', type === 'error' || type === 'warning' ? 'alert' : 'status');
    messageWrapper.setAttribute('aria-labelledby', `${id}-title`);
    messageWrapper.setAttribute('data-drupal-message-id', id);
    messageWrapper.setAttribute('data-drupal-message-type', type);
    let svg = '';

    messageWrapper.innerHTML = `
    <div class="messages__container" data-drupal-selector="messages-container">
      <div class="messages__header${!svg ? ' no-icon' : ''}">
        <h2 class="visually-hidden">${messagesTypes[type]}</h2>
        ${svg}
      </div>
      <div class="messages__content">
        ${text}
      </div>
    </div>
    `;

    Drupal.basecore.closeMessage(messageWrapper);

    return messageWrapper;
  };
})(Drupal);

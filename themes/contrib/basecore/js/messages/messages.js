/**
 * @file
 * Customization of messages.
 */

((Drupal, once) => {
  /**
   * Adds a close button to the message.
   *
   * @param {object} message
   *   The message object.
   */
  const closeMessage = message => {
    const messageContainer = message.querySelector('[data-drupal-selector="messages-container"]');
    if (!messageContainer.querySelector('.messages__button')) {
      const closeBtnWrapper = document.createElement('div');
      closeBtnWrapper.setAttribute('class', 'messages__button');

      const closeBtn = document.createElement('button');
      closeBtn.setAttribute('type', 'button');
      closeBtn.setAttribute('class', 'messages__close');

      const closeBtnText = document.createElement('span');
      closeBtnText.setAttribute('class', 'visually-hidden');
      closeBtnText.innerText = Drupal.t('Close message');

      closeBtn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
      </svg>`;

      messageContainer.appendChild(closeBtnWrapper);
      closeBtnWrapper.appendChild(closeBtn);
      closeBtn.appendChild(closeBtnText);

      closeBtn.addEventListener('click', () => {
        message.classList.add('hidden');
      });
    }
  };

  /**
   * Get messages from context.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the close button behavior for messages.
   */
  Drupal.behaviors.messages = {
    attach(context) {
      once('messages', '[data-drupal-selector="messages"]', context).forEach(closeMessage);
    },
  };
  Drupal.basecore = Drupal.basecore || {};
  Drupal.basecore.closeMessage = closeMessage;
})(Drupal, once);

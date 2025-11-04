/**
 * @file
 * Mailchimp OAuth behaviors used by Ajax
 *
 * @see MailchimpAdminOauthSettingsForm.php
 */
(function (Drupal, drupalSettings) {

  'use strict';

  Drupal.AjaxCommands.prototype.authentication = function (ajax, response, status) {
    // Check the current status.
    function checkStatus() {
      // Create the XMLHttpRequest object.
      let xmlhttp = new XMLHttpRequest();
      // Initialize the request
      xmlhttp.open("GET", `${drupalSettings.mailchimp.middleware_url}/status?domain=${response.domain}&temp_token=${response.temp_token}`);
      // Send the request
      xmlhttp.send();
      // Once the request completes successfully, process the returned status
      xmlhttp.onload = function() {
        processStatus(xmlhttp);
      };
    }

    // Process the status that is returned.
    function processStatus(xmlhttp) {
      if (xmlhttp.readyState === XMLHttpRequest.DONE && xmlhttp.status === 200) {
        // Get and convert the responseText into JSON
        let response = JSON.parse(xmlhttp.responseText);
        const formWrapper = document.querySelector('.mailchimp-admin-oauth-settings');
        const submitButton = formWrapper.querySelector('.form-submit')
          ?? document.querySelector('.gin-sticky-form-actions [form="mailchimp-admin-oauth-settings"].form-submit');
        switch (response.message) {
          case 'pending':
            formWrapper.classList.add('pending');
            if (submitButton) {
              submitButton.setAttribute("disabled", "disabled");
            }
            setTimeout(checkStatus, 3000);
            break;

          case 'error':
            formWrapper.classList.remove('pending');
            if (submitButton) {
              submitButton.removeAttribute("disabled");
            }
            formWrapper.classList.add('error');
            break;

          case 'accepted':
            formWrapper.classList.remove('pending');
            const url = `${window.location.href}/retrieve-token/token/${response.temp_token}/${drupalSettings.mailchimp.csrf_token}`;
            window.location.href = url;
            break;
        }
      }
    }
    checkStatus();
    window.open(response.url);
  }

} (Drupal, drupalSettings));

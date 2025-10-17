jQuery(document).ready(function ($) {
  var defaultValue = 'Your email address';
  $( ".block-mailchimp-signup .form-email" ).val(defaultValue);
  $( ".block-mailchimp-signup .form-email" ).blur(function() {
    if (this.value == '') {
      this.value = defaultValue;
    }
  });
  $( ".block-mailchimp-signup .form-email" ).focus(function() {
    if (this.value == defaultValue) {
      this.value = '';
    }
  });
});
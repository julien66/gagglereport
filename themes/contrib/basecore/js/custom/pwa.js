jQuery(document).ready(function($) {
  var deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    deferredPrompt = e;
    deferredPrompt.prompt();
  });
});

(function (drupalSettings) {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register(drupalSettings.basecore.serviceWorkerPath).then(function(registration) {
        // Registration was successful.
        console.log('ServiceWorker registration successful with scope: ', registration.scope);
      }, function(err) {
        // Registration failed.
        console.error('ServiceWorker registration failed: ', err);
      });
    });
  } else {
    console.error('ServiceWorker is not supported.');
  }
})(drupalSettings);

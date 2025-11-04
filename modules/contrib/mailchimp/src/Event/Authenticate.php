<?php

namespace Drupal\mailchimp\Event;

use Drupal\Component\EventDispatcher\Event;

/**
* Event that is fired when initially authenticated to a Mailchimp instance.
*/
class Authenticate extends Event {

    const AUTHENTICATE = 'authenticate';

    public function __construct() {}

}

<?php

namespace Drupal\hook_event_dispatcher\Generators;

use DrupalCodeGenerator\Command\ModuleGenerator;
use DrupalCodeGenerator\Utils;

/**
 * Class EventGenerator.
 */
class EventGenerator extends ModuleGenerator {

  /**
   * {@inheritdoc}
   */
  protected string $name = 'hook:event-dispatcher';

  /**
   * {@inheritdoc}
   */
  protected string $description = 'Generates hook event dispatcher class and kernel test';

  /**
   * {@inheritdoc}
   */
  protected string $templatePath = __DIR__ . '/../../templates';

  /**
   * {@inheritdoc}
   */
  protected function generate(array &$vars): void {
    $this->collectDefault($vars);

    /** @var callable $validator */
    $validator = [$this, 'validateRequired'];

    $vars['event'] = $this->choice('Type of event', [
      'hook' => 'Hook',
      'alter' => 'Alter',
    ]);
    $vars[$vars['event']] = $this->ask('Hook/Alter name (without <options=bold>hook_</> prefix and <options=bold>_alter</> suffix)', NULL, $validator);
    $vars['id'] = $vars['event'] === 'hook' ? $vars[$vars['event']] : $vars[$vars['event']] . '_alter';
    $vars['sub_namespace'] = $this->ask('Sub namespace');

    $vars['event_name'] = Utils::camelize($vars['id']);
    $vars['class'] = $vars['event_name'] . 'Event';
    $vars['type'] = strtoupper($vars['id']);

    $this->addFile($vars['sub_namespace'] ? 'src/Event/{sub_namespace}/{class}.php' : 'src/Event/{class}.php')->template('event.twig');
    $this->addFile($vars['sub_namespace'] ? 'tests/src/Kernel/{sub_namespace}/{class}Test.php' : 'tests/src/Kernel/{class}Test.php')->template('kernel.twig');
  }

}

<?php

declare(strict_types=1);

namespace Drupal\hook_event_dispatcher\Drush\Generators;

use DrupalCodeGenerator\Asset\AssetCollection as Assets;
use DrupalCodeGenerator\Attribute\Generator;
use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\GeneratorType;
use DrupalCodeGenerator\Utils;
use DrupalCodeGenerator\Validator\Required;

#[Generator(
  name: 'hook:event-dispatcher',
  description: 'Generates hook event dispatcher class and kernel test',
  templatePath: __DIR__ . '/../../../templates',
  type: GeneratorType::MODULE_COMPONENT,
)]
final class EventGenerator extends BaseGenerator {

  /**
   * {@inheritdoc}
   */
  protected function generate(array &$vars, Assets $assets): void {
    $ir = $this->createInterviewer($vars);

    $vars['machine_name'] = $ir->askMachineName();
    $vars['name'] = $ir->askName();
    $vars['event'] = $ir->choice('Type of event', [
      'hook' => 'Hook',
      'alter' => 'Alter',
    ]);
    $vars[$vars['event']] = $ir->ask('Hook/Alter name (without <options=bold>hook_</> prefix and <options=bold>_alter</> suffix)', NULL, new Required());
    $vars['id'] = $vars['event'] === 'hook' ? $vars[$vars['event']] : $vars[$vars['event']] . '_alter';
    $vars['sub_namespace'] = $ir->ask('Sub namespace');

    $vars['event_name'] = Utils::camelize($vars['id']);
    $vars['class'] = $vars['event_name'] . 'Event';
    $vars['type'] = strtoupper($vars['id']);

    $assets->addFile($vars['sub_namespace'] ? 'src/Event/{sub_namespace}/{class}.php' : 'src/Event/{class}.php', 'event.twig');
    $assets->addFile($vars['sub_namespace'] ? 'tests/src/Kernel/{sub_namespace}/{class}Test.php' : 'tests/src/Kernel/{class}Test.php', 'kernel.twig');
  }

}

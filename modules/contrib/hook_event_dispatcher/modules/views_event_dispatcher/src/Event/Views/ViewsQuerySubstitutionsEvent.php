<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsQuerySubstitutionEvent.
 */
#[HookEvent(id: 'views_query_substitutions', hook: 'views_query_substitutions')]
final class ViewsQuerySubstitutionsEvent extends AbstractViewsEvent implements HookReturnInterface {

  /**
   * Views query substitutions.
   *
   * @var array
   */
  private array $substitutions = [];

  /**
   * Get the query substitutions.
   *
   * @return array
   *   An associative array where each key is a string to be replaced, and the
   *   corresponding value is its replacement. The strings to replace are often
   *   surrounded with '***', as illustrated in the example implementation, to
   *   avoid collisions with other values in the query.
   */
  public function &getSubstitutions(): array {
    return $this->substitutions;
  }

  /**
   * Add a substitution.
   *
   * @param string $target
   *   String target to be replaced.
   * @param string $replacement
   *   The replacement of the given target.
   */
  public function addSubstitution(string $target, string $replacement): void {
    $this->substitutions[$target] = $replacement;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_QUERY_SUBSTITUTIONS;
  }

  /**
   * {@inheritdoc}
   */
  public function &getReturnValue() {
    return $this->getSubstitutions();
  }

}

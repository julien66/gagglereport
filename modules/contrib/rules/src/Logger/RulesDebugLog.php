<?php

namespace Drupal\rules\Logger;

use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// cspell:ignore thisline

/**
 * Logger that stores Rules debug logs with the session service.
 *
 * This logger stores an array of Rules debug logs in the session under
 * the attribute named 'rules_debug_log'.
 */
class RulesDebugLog implements LoggerInterface {
  use LoggerTrait;
  use StringTranslationTrait;

  /**
   * Local storage of log entries.
   *
   * @var array
   */
  protected $logs = [];

  /**
   * The session service.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * Constructs a RulesDebugLog object.
   *
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session service.
   */
  public function __construct(SessionInterface $session) {
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = []): void {
    // Remove any backtraces since they may contain an unserializable variable.
    unset($context['backtrace']);

    $localCopy = $this->session->get('rules_debug_log', []);

    // Append the new log to the $localCopy array.
    // In D7:
    // @code
    //   logs[] = [$msg, $args, $priority, microtime(TRUE), $scope, $path];
    // @endcode
    $localCopy[] = [
      'message' => $message,
      'context' => $context,
      /** @var \Psr\Log\LogLevel $level */
      'level' => $level,
      'timestamp' => $context['timestamp'],
      'scope' => $context['scope'],
      'path' => $context['path'],
    ];

    // Write the $localCopy array back into the session;
    // it now includes the new log.
    $this->session->set('rules_debug_log', $localCopy);
  }

  /**
   * Returns a structured array of log entries.
   *
   * @return array
   *   Array of stored log entries, keyed by an integer log line number. Each
   *   element of the array contains the following keys:
   *   - message: The log message, optionally with FormattedMarkup placeholders.
   *   - context: An array of message placeholder replacements.
   *   - level: \Psr\Log\LogLevel level.
   *   - timestamp: Microtime timestamp in float format.
   *   - scope: TRUE if there are nested logs for this entry, FALSE if this is
   *     the last of the nested entries.
   *   - path: Path to edit this component.
   */
  public function getLogs(): array {
    return (array) $this->session->get('rules_debug_log');
  }

  /**
   * Clears the logs entries from the storage.
   */
  public function clearLogs(): void {
    $this->session->remove('rules_debug_log');
  }

  /**
   * Renders the whole log.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   An string already rendered to HTML.
   */
  public function render() {
    $build = $this->build();
    return \Drupal::service('renderer')->renderInIsolation($build);
  }

  /**
   * Assembles the entire log into a render array.
   *
   * @return array
   *   A Drupal render array.
   */
  public function build(): array {
    $this->logs = $this->getLogs();

    if (count($this->logs) == 0) {
      // Nothing to render.
      return [];
    }
    // Container for all log entries.
    $build = [
      '#type' => 'details',
      // @codingStandardsIgnoreStart
      '#title' => $this->t('Rules evaluation log') . '<span class="rules-debug-open-all">-Open all-</span>',
      // @codingStandardsIgnoreEnd
      '#attributes' => ['class' => ['rules-debug-log']],
    ];

    $line = 0;
    while (isset($this->logs[$line])) {
      // Each event is in its own 'details' wrapper so the details of
      // evaluation may be opened or closed.
      $build[$line] = [
        '#type' => 'details',
        // @codingStandardsIgnoreStart
        // Need to filter out context keys that aren't recognized as
        // placeholders for t(), because Drupal core no longer supports these.
        '#title' => $this->t(
          $this->logs[$line]['message'],
          $this->filterContext($this->logs[$line]['context'])
        ),
        // @codingStandardsIgnoreEnd
      ];
      // $line is modified inside renderHelper().
      $thisline = $line;
      $build[$thisline][] = $this->renderHelper($line);
      $line++;
    }

    return $build;
  }

  /**
   * Renders the log of one event invocation.
   *
   * Called recursively, consuming all the log lines for this event.
   *
   * @param int $line
   *   The line number of the log, starting at 0.
   *
   * @return array
   *   A render array.
   */
  protected function renderHelper(int &$line = 0): array {
    $build = [];
    $startTime = $this->logs[$line]['timestamp'];
    while ($line < count($this->logs)) {
      if ($build && !empty($this->logs[$line]['scope'])) {
        // This next entry stems from another evaluated set so we create a
        // new container for its log messages then fill that container with
        // a recursive call to renderHelper().
        $link = NULL;
        if (isset($this->logs[$line]['path'])) {
          $link = Link::fromTextAndUrl($this->t('edit'), Url::fromUserInput('/' . $this->logs[$line]['path']))->toString();
        }
        $build[$line] = [
          '#type' => 'details',
          // @codingStandardsIgnoreStart
          // Need to filter out context keys that aren't recognized as
          // placeholders for t(), because Drupal core no longer supports these.
          '#title' => $this->t(
            $this->logs[$line]['message'],
            $this->filterContext($this->logs[$line]['context'])
          ) . ' [' . $link . ']',
          // @codingStandardsIgnoreEnd
        ];
        $thisline = $line;
        $build[$thisline][] = $this->renderHelper($line);
      }
      else {
        // This next entry is a leaf of the evaluated set so we just have to
        // add the details of the log entry.
        $link = NULL;
        if (isset($this->logs[$line]['path']) && !isset($this->logs[$line]['scope'])) {
          $link = [
            'title' => $this->t('edit'),
            'url' => Url::fromUserInput('/' . $this->logs[$line]['path']),
          ];
        }
        $build[$line] = [
          '#theme' => 'rules_debug_log_element',
          '#starttime' => $startTime,
          '#timestamp' => $this->logs[$line]['timestamp'],
          '#level' => $this->logs[$line]['level'],
          // @codingStandardsIgnoreStart
          // Need to filter out context keys that aren't recognized as
          // placeholders for t(), because Drupal core no longer supports these.
          '#text' => $this->t(
            $this->logs[$line]['message'],
            $this->filterContext($this->logs[$line]['context'])
          ),
          // @codingStandardsIgnoreEnd
          '#link' => $link,
        ];

        if (isset($this->logs[$line]['scope']) && !$this->logs[$line]['scope']) {
          // This was the last log entry of this set.
          return [
            '#theme' => 'item_list',
            '#items' => $build,
          ];
        }
      }
      $line++;
    }

    return [
      '#theme' => 'item_list',
      '#items' => $build,
    ];
  }

  /**
   * Removes invalid placeholders from the given array.
   *
   * As of Drupal 10, arrays that contain placeholder replacement strings for
   * use by the core Drupal t() function may not contain any keys that aren't
   * valid placeholders for the string being translated. That means we have to
   * remove keys from these arrays before passing them to the t() function.
   *
   * @param array $context
   *   An array containing placeholder replacements for use by t(), keyed by
   *   the placeholder.
   *
   * @return array
   *   The context array, with invalid placeholders removed.
   *
   * @see \Drupal\Component\Render\FormattableMarkup::placeholderFormat()
   */
  protected function filterContext(array $context = []): array {
    // This implementation assumes that all valid placeholders start with a
    // punctuation character. In reality Drupal currently supports only '@',
    // '%', and ':', but testing for just those three is considerably slower
    // than using the built-in PHP ctype_punct() function. This will work to
    // remove all invalid placeholders added by the Rules module, but another
    // invalid placeholder added by a user might fall through and still cause
    // an error (as it should, to indicate the user has made an error).
    return array_filter($context, function ($key) {
      return ctype_punct($key[0]);
    }, ARRAY_FILTER_USE_KEY);
  }

}

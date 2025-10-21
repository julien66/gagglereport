<?php

/**
 * @file
 * Builds placeholder replacement tokens for values specific to Poll nodes.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function poll_token_info() {
  $node['votes'] = [
    'name' => t("Poll votes"),
    'description' => t("The number of votes that have been cast on a poll."),
  ];
  $node['winner'] = [
    'name' => t("Poll winner"),
    'description' => t("The winning poll answer."),
  ];
  $node['winner-votes'] = [
    'name' => t("Poll winner votes"),
    'description' => t("The number of votes received by the winning poll answer."),
  ];
  $node['winner-percent'] = [
    'name' => t("Poll winner percent"),
    'description' => t("The percentage of votes received by the winning poll answer."),
  ];
  $node['duration'] = [
    'name' => t("Poll duration"),
    'description' => t("The length of time the poll is set to run."),
  ];

  return [
    'tokens' => ['poll' => $node],
  ];
}

/**
 * Implements hook_tokens().
 */
function poll_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type == 'poll' && !empty($data['poll'])) {
    /** @var \Drupal\poll\Entity\Poll $poll */
    $poll = $data['poll'];

    $total_votes = 0;
    $highest_votes = 0;
    foreach ($poll->getVotes() as $vote) {
      $total_votes += $vote;
    }
    $options = $poll->getOptions();
    foreach ($poll->getVotes() as $pid => $vote) {
      if ($vote > $highest_votes) {
        $winner = $options[$pid];
        $highest_votes = $vote;
      }
    }
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'votes':
          $replacements[$original] = $total_votes;
          break;

        case 'winner':
          if (isset($winner)) {
            $replacements[$original] = $winner;
          }
          else {
            $replacements[$original] = '';
          }
          break;

        case 'winner-votes':
          if (isset($winner)) {
            $replacements[$original] = $highest_votes;
          }
          else {
            $replacements[$original] = '';
          }
          break;

        case 'winner-percent':
          if (isset($winner)) {
            $percent = ($highest_votes / $total_votes) * 100;
            $replacements[$original] = number_format($percent, 0);
          }
          else {
            $replacements[$original] = '';
          }
          break;

        case 'duration':
          $replacements[$original] = \Drupal::service('date.formatter')->formatInterval($poll->getRuntime());
          break;
      }
    }
  }

  return $replacements;
}

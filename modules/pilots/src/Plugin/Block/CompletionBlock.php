<?php
namespace Drupal\pilots\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Annotation\Translation;

/**
 * Provides Profile Completion  Block.
 *
 * @Block(
 *   id = "completion_block",
 *   admin_label = @Translation("Completion Block"),
 *   )
 */
class CompletionBlock extends BlockBase {
   /**
    * {@inheritdoc}
    */
    public function build() {
	$query = \Drupal::entityQuery('user')->accessCheck(True);
	$totalUser = $query->count()->execute();
	$query->exists('field_body_weight');
	$query->exists('field_total_weight_in_flight');
	$query->exists('field_maximum_glider_weight');
	$query->exists('field_civlid');
	$completeWeight = $query->count()->execute();
	$percent = floor($completeWeight * 100 / $totalUser);
	return [
	    '#theme' => 'completion_block',
	    '#data' => ['total_user' => $totalUser, 'complete' => $completeWeight, 'percent' => $percent ],
    ];
  }
}

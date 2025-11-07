<?php
namespace Drupal\pilots\Controller;
error_reporting(E_ALL);
ini_set('display_errors', 'on');

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller routines for page example routes.
 */
class WeightPageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'pilots';
  }

  
  /*public static function create(ContainerInterface $container) {
	  $dataBuilder = $container->get('pilots.data_builder');
	  return new static($dataBuilder);
  }

  public function __construct($dataBuilder) {
  	$this->data_builder = $dataBuilder;
  }*/

  /**
   * Constructs a pilots weight data page.
   *
   * The router _controller callback, maps the path
   * 'pilots/WeightPage/simple' to this method.
   *
   * _controller callbacks return a renderable array for the content area of the
   * page. The theme system will later render and surround the content with the
   * appropriate blocks, navigation, and styling.
   */
  public function weightpage($minrank, $maxrank) {
    if (!is_numeric($minrank) || !is_numeric($maxrank) || ($maxrank > $minrank)) {
      // We will just show a standard "access denied" page in this case.
      throw new AccessDeniedHttpException();
    }
    $query = \Drupal::entityQuery('user')->accessCheck(TRUE);
    $query->exists('field_body_weight');
    $query->exists('field_total_weight_in_flight');
    $query->exists('field_maximum_glider_weight');
    $query->exists('field_civlid');
    $query->condition('field_wprs', $minrank,'<=');
    $query->condition('field_wprs', $maxrank,'>=');
    $query->sort('field_body_weight', 'ASC');
    $userList = $query->execute();
    $entityStorage = \Drupal::entityTypeManager()->getStorage('user');
    $users = $entityStorage->loadMultiple($userList);
    $weights = [];
    $ballast = [];
    foreach($users as $user) {
	$bodyWeight = (int) $user->field_body_weight->value;
	$totalWeight = (int) $user->field_total_weight_in_flight->value;
    	$weights[] = [$bodyWeight, $totalWeight]; 
    	$ballast[] =  ($totalWeight - $bodyWeight);
    	$ballastWeight[] =  [$bodyWeight, ($totalWeight - $bodyWeight)];
    }
    $labels = \Drupal::service('pilots.data_builder')->setLabelFromDataRange($weights, 10);
    $bodyDataset = \Drupal::service('pilots.data_builder')->addDataSetFromDataRange(array_column($weights,0));
    $totalDataset = \Drupal::service('pilots.data_builder')->addDataSetFromDataRange(array_column($weights,1));
    $ballastLabel = \Drupal::service('pilots.data_builder')->setLabelFromDataRange(array($ballast), 5);
    $ballastDataset = \Drupal::service('pilots.data_builder')->addDataSetFromDataRange($ballast);
    $ballastBodyLabel = \Drupal::service('pilots.data_builder')->setLabelFromDataRange(array(array_column($weights,0)),10);
    $ballastBodyDataset = \Drupal::service('pilots.data_builder')->addAssocMeanDataSetFromDataRange($ballastWeight);
    return [
	    '#theme'=> 'weight_data_page',
	    '#data' => [
		    'minRank' => $minrank,
		    'maxRank' => $maxrank,
		    'count' => count($users),
		    'body' => $bodyDataset, 
		    'total' => $totalDataset, 
		    'label' => $labels,
		    'ballastBodyLabel' => $ballastBodyLabel,
		    'ballastBodyDataset' => $ballastBodyDataset,
		    'ballastLabel' => $ballastLabel, 
		    'ballast' => $ballastDataset
	    ],
    ];
  }
}

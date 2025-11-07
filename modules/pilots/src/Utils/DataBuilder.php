<?php

namespace Drupal\pilots\Utils;

class DataBuilder {
	/*
	 * That's a chart data object, ready to be passed to chart.js api.
	 */
	protected $chart_data;	
	/*
	 * Min range of data
	 */
	protected $min;
	/*
	 * Max range of data
	 */
	protected $max;
	/*
	 *  Range of data between min and max
	 */
	protected $range;


	/*
	 * At construction, we instanciante usefull properties.
	 */
	public function __construct(){
		$this->chart_data['labels'] = [];
		$this->chart_data['datasets'] = [];
		$this->min = 0;
		$this->max = 20;
		$this->range = 1;
	}

	/*
	 * setLabelFromDataRange
	 * @params
	 * array $data;
	 * int $range;
	 * This set chart labels directly from data numbers according to a given range.
	 * eg. $data = [[36,56,70],[105,120]] with a $range of 10 will provide ['31-40', '41-50', '51-60'...]
	 * It set min max and range for further data operation.
	 * Accept multidimensionnal array.
	 **/
	public function setLabelFromDataRange($data, $range) {
		$this->chart_data['labels'] = [];
		$this->range = $range;
		$flat_data = (array_merge(...$data));
		sort($flat_data);
		$min = $this->min = (int) floor(reset($flat_data) /10) * 10;
		$max = $this->max = (int) ceil(end($flat_data)/10)*10;
		$i = 1;
		while($min + $i <= $max) {
		    array_push($this->chart_data['labels'], $min + $i . " - " . $min + $i + $range -1);
		    $i += $range;
		}
		return $this->chart_data['labels'];
	}

	/*
	 * addDatasetFromDataRange
	 * @params
	 * array $data
	 * This use current data builder range to increase a counter in each range according to data 
	 * eg. $data = [31, 33, 34, 35, 52, 53] with a range of 10 will return [4,0,2];
	 * Accept flat array.
	 */
	public function addDatasetFromDataRange($data) {
		$min = $this->min;
		$max = $this->max;
		$range = $this->range;	
		$i = 1; $y = 0;
		$dataset = [];
		while($min + $i <= $max) {
		    $dataset[$y] = 0;
		    foreach($data as $number) {
			    if (($number >= ($min + $i)) && ($number <= ($min +$i + $range -1))) {
		    		$dataset[$y]++;
			    }
		    }
		    $i += $range;
		    $y++;
		}
		return $dataset;	
	}


	/*
	 * addDatasetFromDataRange
	 * @params
	 * array $data
	 * This use current data builder range to calculate a mean in each range according to associated data
	 * eg. $data = [[31, 70], [33, 65],[34, 75], [35,70], [52,90], [53,90] with a range of 10 will return [70,0,90];
	 * Accept multidimensional array.
	 */
	public function addAssocMeanDatasetFromDataRange($data) {
		$min = $this->min;
		$max = $this->max;
		$range = $this->range;	
		$i = 1; $y = 0;
		$dataset = [];
		while($min + $i <= $max) {
		    $dataset[$y] = [];
		    foreach($data as $weights) {
			 $bodyWeight =  $weights[0]; 
			 $ballastWeight =  $weights[1];
			 if (($bodyWeight >= ($min + $i)) && ($bodyWeight <= ($min +$i + $range -1))) {
		    	    array_push($dataset[$y], $ballastWeight);
			}

		    }
		    $i += $range;
		    $y++;
		}
		return $this->flattenMeanArray($dataset);	
	
	}

	/*
	 * flattenMeanArray
	 * @params
	 * array $data
	 * This flatten a multidimensional array by returning the mean value of all its components.
	 * eg. $data = [[5, 15, 10, 10],[35, 35], [105, 95, 110, 90, 110, 90] with a range of 10 will return [10, 35, 100];
	 */
	public function flattenMeanArray($data) : Array {
		$meanArrray = [];
		foreach ($data as $set) {
			if (count($set) > 0) {
			    $average = round(array_sum($set) / count($set));
			}
			else {
			    $average = 0;
			}
			$meanArray[] = $average;
		}	
		return $meanArray;

	}	


	public function getChartData(): Object {
		return $this->chart_data;
	}
}

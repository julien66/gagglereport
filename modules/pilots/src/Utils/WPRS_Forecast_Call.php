<?php
namespace Drupal\pilots\Utils;

class WPRS_Forecast_Call {

	protected $apiKey;
	protected $bestRanks;
	
	public function __construct(){
	    /*
	     * Read the secret api key provided by Stephan Schope (WPRS Forecast).
	     * It is used it to get the current ranking.
	    */
	    $apiFile = file_get_contents(__DIR__ . '/.apiKey.txt');
	    $this->apiKey = str_replace(array('.', ' ', "\n", "\t", "\r"), '', $apiFile); 
	    /*
	     * Read the CSV provided by Kuba Sto.
	     * It is used to get the best WPRS ranking ever achieved. (@todo CSV need update).
	    */ 
	    $bestFile = fopen(__DIR__ . '/top_rank.csv', 'r');
	    while (($row = fgetcsv($bestFile, escape : '\\')) !== FALSE) {
              $this->bestRanks[$row[0]] = $row[3];
	    }
	    fclose($bestFile);
	} 

	/*
	 * getCurrentRank
	 * @param : int $civlid CIVL pilot unique id
	 * Send the call to Stephan API
	 * Return the current rank. 
	 */
	public function getCurrentRank($civlid) : int {
	    $client = \Drupal::httpClient();
	    $request = $client->get('https://wprs-forecast.org/api/worldranking/civl-id?apiKey=' . $this ->apiKey . '&civlId=' . (int)$civlid);
	    $response = json_decode($request->getBody());
	    return isset($response[0]->rank) ? $response[0]->rank : 0;
	}
	
	/*
	 * getBestRank
	 * @param : int $civlid CIVL pilot unique id
	 * Retrieve the best rank in the array representing Kuba's CSV file
	 * Return the best rank. 
	 */
	public function getBestRank($civlid) : int {
		return isset($this->bestRanks[$civlid]) ? $this->bestRanks[$civlid] : 0;
	} 
}

<?php
/**
 * AutoComplete Field - PHP Remote Script
 *
 * This is a sample source code provided by fromvega.
 * Search for the complete article at http://www.fromvega.com
 *
 * Enjoy!
 *
 * @author fromvega
 *
 */

// define the colors array
$best = get_recommend_goods('best');
//print_r($best);
$name = array('black', 'blue', 'brown', 'green', 'grey',
		'gold', 'navy', 'orange', 'pink', 'silver',
		'violet', 'yellow', 'red','孟谦');
$colors = array();
for($i=0;$i<count($name);$i++){
	$colors[$i]['name'] = $name[$i];
	$colors[$i]['pic'] = $i.".jpg";
}
//print_r($colors);


// check the parameter
if(isset($_GET['part']) and $_GET['part'] != '')
{
	// initialize the results array
	$results = array();

	// search colors
	$index = 0;
	foreach($colors as $item)
	{
		// if it starts with 'part' add to results
		if( strpos($item['name'], $_GET['part']) === 0 ){
			$results[$index]['name'] = $item['name'];
			$results[$index]['pic']  = $item['pic'];
			//$results['address'] = $color." address";
		}
		$index ++;
	}

	// return the array as json with PHP 5.2
	echo json_encode($results);

	// or return using Zend_Json class
	//require_once('Zend/Json/Encoder.php');
	//echo Zend_Json_Encoder::encode($results);
}
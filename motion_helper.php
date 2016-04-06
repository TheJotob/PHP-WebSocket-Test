<?php
class MotionHelper {

  public static $thresholds = array();

  /**
  	* Check if the threshold is exceeded
  	* @param float[] $data
    * @return boolean
  	*/
  public static function thresholdExceeded($client, $data) {
  	for($i = 0; $i < count($data); $i++) {
  		// check if the acceleration is higher or lower than the threshold
  		if($data[$i] > self::getThreshold($client) || $data[$i] < (self::getThreshold($client) * -1)) {
  			echo "Warning: " . $data[$i] . "\n";
  			return true;
  		}
  	}
  }

  public static function setThreshold($client, $threshold) {
  	$index = array_search($client, SocketHelper::$clients);
  	self::$thresholds[$index] = $threshold;
  	// echo "Treshold for " . $index . " is: " . $threshold;
  }

  public static function getThreshold($client) {
  	$index = array_search($client, SocketHelper::$clients);
  	if(isset(self::$thresholds[$index]))
  		return self::$thresholds[$index];
  	else {
  		return Config::DEFAULT_THRESHOLD;
  	}
  }

  /**
  	* Parse the acceleration data.
  	* @param String $str format: "xAcceleration, yAcceleration, zAcceleration"
    * @return float[] or false
  	*/
  function parseAccelerationData($str) {
  	$accelerationData = explode(", ", $str);

  	// check if the array has the right amount of values (should be 3 because of x, y, z)
  	if(count($accelerationData) != 3)
  		return false;

  	// check if all values are containing floats
  	for($i = 0; $i < 3; $i++) {
  		if(is_float(floatval($accelerationData[$i])))
  			$accelerationData[$i] = floatval($accelerationData[$i]);
  		else return false;
  	}

  	return $accelerationData;
  }
}

<?php
/**
 * @package	ZapCalLib
 * @author	Dan Cogliano <http://zcontent.net>
 * @copyright   Copyright (C) 2006 - 2017 by Dan Cogliano
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	http://icalendar.org/php-library.html
 */

namespace baerla\ICalendar;

/**
 * Object for storing an unfolded iCalendar line
 *
 * The ZCiCalDataNode class contains data from an unfolded iCalendar line
 *
 */
class ZCiCalDataNode {
	/**
	 * The name of the node
	 *
	 * @var string
	 */
	var $name = "";

	/**
	 * Node parameters (before the colon ":")
	 *
	 * @var array
	 */
	var $parameter=array();

	/**
	 * Node values (after the colon ":")
	 * 
	 * @var array
	 */
	var $value=array();

	/**
 	 * Create an object from an unfolded iCalendar line
 	 *
 	 * @param string $line An unfolded iCalendar line
 	 *
 	 * @return void
 	 *
 	 */
	function __construct( $line ) {
		//echo "ZCiCalDataNode($line)<br/>\n";
		//separate line into parameters and value
		// look for colon separating name or parameter and value
		// first change any escaped colons temporarily to make it easier
		$tline = str_replace("\\:", "`~", $line);
		// see if first colon is inside a quoted string
		$i = 0;
		$datafind = false;
		$inquotes = false;
		while(!$datafind && ($i < strlen($tline))) {
			//echo "$i: " . $tline{$i} . ", ord() = " . ord($tline{$i}) . "<br>\n";
			if(!$inquotes && $tline{$i} == ':')
				$datafind=true;
			else{
				$i += 1;
				if(substr($tline,$i,1) == '"')
					$inquotes = !$inquotes;
			}
		}
		if($datafind){
			$value = str_replace("`~","\\:",substr($line,$i+1));
			// fix escaped characters (don't see double quotes in spec but Apple apparently uses it in iCal)
			$value = str_replace(array('\\N' , '\\n', '\\"' ), array("\n", "\n" , '"'), $value);
			$tvalue = str_replace("\\,", "`~", $value);
			//echo "value: " . $tvalue . "<br>\n";
			$tvalue = explode(",",$tvalue);
			$value = str_replace("`~","\\,",$tvalue);
			$this->value = $value;
		}

		$parameter = trim(substr($line,0,$i));

		$parameter = str_replace("\\;", "`~", $parameter);
		$parameters = explode(";", $parameter);
		$parameters = str_replace("`~", "\\;", $parameters);
		$this->name = array_shift($parameters);
		foreach($parameters as $parameter){
			$pos = strpos($parameter,"=");
			if($pos > 0){
				$param = substr($parameter,0,$pos);
				$paramvalue = substr($parameter,$pos+1);
				$tvalue = str_replace("\\,", "`~", $paramvalue);
				//$tvalue = explode(",",$tvalue);
				$paramvalue = str_replace("`~","\\,",$tvalue);
				$this->parameter[strtolower($param)] = $paramvalue;
				//$this->paramvalue[] = $paramvalue;
			}
		}
	}

/**
 * getName()
 *
 * Return the name of the object
 *
 * @return string
 */
	function getName(){
		return $this->name;
	}

/**
 * Get $ith parameter from array
 * @param int $i
 * 
 * @return var
 */
	function getParameter($i){
		return $this->parameter[$i];
	}

/**
 * Get parameter array
 *
 * @return array
 */
	function getParameters(){
		return $this->parameter;
	}

/**
 * Get comma separated values
 * 
 * @return string
 */
	function getValues(){
		return implode(",",$this->value);
	}
}

?>

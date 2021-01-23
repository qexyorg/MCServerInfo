<?php

/**
 * Formatter class of MCServerInfo
 *
 * @author Qexy admin@qexy.org
 *
 * @package qexyorg\MCServerInfo
 *
 * @license MIT
 *
 * @version 1.0.0
 */

namespace qexyorg\MCServerInfo;

class Formatter {

	/**
	 *
	 * Color code pattern for search in description
	 *
	*/
	public static $COLOR_CODE_PATTERN = '[0-9a-f]|k|l|m|n|o|r';

	/**
	 *
	 * Color code symbol. Usually used § or &
	 *
	*/
	public static $COLOR_CODE_PREFIX = '§';

	/**
	 *
	 * Prefix class for html tags
	 *
	 * @example <span class="mcsi_*">...</span>
	 *
	*/
	public static $COLOR_CODE_STYLE_PREFIX = 'mcsi_';

	/**
	 *
	 * Settings for color codes
	 *
	*/
	public static $COLORS = [
		'black' => ['name' => 'black', 'code' => '0', 'hex' => '000000', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'color:#000000;'],
		'dark_blue' => ['name' => 'dark_blue', 'code' => '1', 'hex' => '0000AA', 'r' => 0, 'g' => 0, 'b' => 170, 'styles' => 'color:#0000AA;'],
		'dark_green' => ['name' => 'dark_green', 'code' => '2', 'hex' => '00AA00', 'r' => 0, 'g' => 170, 'b' => 0, 'styles' => 'color:#00AA00;'],
		'dark_aqua' => ['name' => 'dark_aqua', 'code' => '3', 'hex' => '00AAAA', 'r' => 0, 'g' => 170, 'b' => 170, 'styles' => 'color:#00AAAA;'],
		'dark_red' => ['name' => 'dark_red', 'code' => '4', 'hex' => 'AA0000', 'r' =>170, 'g' => 0, 'b' => 0, 'styles' => 'color:#AA0000;'],
		'dark_purple' => ['name' => 'dark_purple', 'code' => '5', 'hex' => 'AA00AA', 'r' => 170, 'g' => 0, 'b' => 170, 'styles' => 'color:#AA00AA;'],
		'gold' => ['name' => 'gold', 'code' => '6', 'hex' => 'FFAA00', 'r' => 255, 'g' => 170, 'b' => 0, 'styles' => 'color:#FFAA00;'],
		'gray' => ['name' => 'gray', 'code' => '7', 'hex' => 'AAAAAA', 'r' => 170, 'g' => 170, 'b' => 170, 'styles' => 'color:#AAAAAA;'],
		'dark_gray' => ['name' => 'dark_gray', 'code' => '8', 'hex' => '555555', 'r' => 85, 'g' => 85, 'b' => 85, 'styles' => 'color:#555555;'],
		'blue' => ['name' => 'blue', 'code' => '9', 'hex' => '5555FF', 'r' => 85, 'g' => 85, 'b' => 255, 'styles' => 'color:#5555FF;'],
		'green' => ['name' => 'green', 'code' => 'a', 'hex' => '55FF55', 'r' => 85, 'g' => 255, 'b' => 85, 'styles' => 'color:#55FF55;'],
		'aqua' => ['name' => 'aqua', 'code' => 'b', 'hex' => '55FFFF', 'r' => 85, 'g' => 255, 'b' => 255, 'styles' => 'color:#55FFFF;'],
		'red' => ['name' => 'red', 'code' => 'c', 'hex' => 'FF5555', 'r' => 255, 'g' => 85, 'b' => 85, 'styles' => 'color:#FF5555;'],
		'light_purple' => ['name' => 'light_purple', 'code' => 'd', 'hex' => 'FF55FF', 'r' => 255, 'g' => 85, 'b' => 255, 'styles' => 'color:#FF55FF;'],
		'yellow' => ['name' => 'yellow', 'code' => 'e', 'hex' => 'FFFF55', 'r' => 255, 'g' => 255, 'b' => 85, 'styles' => 'color:#FFFF55;'],
		'white' => ['name' => 'white', 'code' => 'f', 'hex' => 'FFFFFF', 'r' => 255, 'g' => 255, 'b' => 255, 'styles' => 'color:#FFFFFF;'],
		'minecoin_gold' => ['name' => 'minecoin_gold', 'code' => 'g', 'hex' => 'DDD605', 'r' => 221, 'g' => 214, 'b' => 5, 'styles' => 'color:#DDD605;'],

		'strikethrough' => ['name' => 'strikethrough', 'code' => 'm', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'text-decoration:line-through;'],
		'obfuscated' => ['name' => 'obfuscated', 'code' => 'k', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'color:transparent;text-shadow:0 0 5px rgba(0,0,0,0.5);'],
		'bold' => ['name' => 'bold', 'code' => 'l', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'font-weight:bold;'],
		'italic' => ['name' => 'italic', 'code' => 'o', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'font-style:italic;'],
		'underline' => ['name' => 'underline', 'code' => 'n', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => 'text-decoration:underline;'],
		'reset' => ['name' => 'reset', 'code' => 'r', 'hex' => '', 'r' => 0, 'g' => 0, 'b' => 0, 'styles' => ''],
	];

	/**
	 *
	 * Method for to string by "ping" data
	 *
	 *
	 * @param $data array
	 *
	 *
	 * @return string
	 *
	*/
	public static function parseExtra(array $data) : string {
		$string = "";

		foreach($data as $ar){
			if(@$ar['strikethrough']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['strikethrough']['code'];
			}

			if(@$ar['bold']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['bold']['code'];
			}

			if(@$ar['italic']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['italic']['code'];
			}

			if(@$ar['underline']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['underline']['code'];
			}

			if(@$ar['obfuscated']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['obfuscated']['code'];
			}

			if(@$ar['reset']){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS['reset']['code'];
			}

			if(isset($ar['color']) && isset(self::$COLORS[$ar['color']])){
				$string .= self::$COLOR_CODE_PREFIX.self::$COLORS[$ar['color']]['code'];
			}

			$string .= @$ar['text'].self::$COLOR_CODE_PREFIX.self::$COLORS['reset']['code'];
		}

		return $string;
	}

	/**
	 *
	 * Remove all Minecraft style codes from description string
	 *
	 *
	 * @param $string string
	 *
	 *
	 * @return string
	 *
	*/
	public static function colorCodesStrip(string $string) : string {
		return preg_replace('/'.self::$COLOR_CODE_PREFIX.'('.self::$COLOR_CODE_PATTERN.')/i', '', $string);
	}

	/**
	 * Replace style codes from description string
	 *
	 *
	 * @param $string string
	 *
	 *
	 * @return string
	 *
	*/
	public static function colorCodesToHTML(string $string) : string {
		if(empty($string)){ return $string; }

		if(!preg_match_all('/'.self::$COLOR_CODE_PREFIX.'('.self::$COLOR_CODE_PATTERN.')/i', $string, $matches)){
			return $string;
		}

		if(!isset($matches[1]) || empty($matches[1])){
			return $string;
		}

		$colors = [];

		foreach(self::$COLORS as $ar){ $colors[$ar['code']] = $ar; }

		$opened = $closed = 0;

		$max = 1;

		foreach($matches[1] as $match){

			if($match != 'r' && isset($colors[$match])){

				$styles = "";

				if($colors[$match]['styles'] != ''){
					$styles = "style=\"{$colors[$match]['styles']}\"";
				}

				$string = str_replace(self::$COLOR_CODE_PREFIX.$match, '<span class="'.self::$COLOR_CODE_STYLE_PREFIX.$colors[$match]['name']."\" {$styles}>", $string, $max);

				$opened++;
			}

			if($match == 'r' && $opened > $closed){
				$repeat = str_repeat("</span>", $opened - $closed);

				$string = str_replace(self::$COLOR_CODE_PREFIX.$match, $repeat, $string, $max);
				$closed = $opened;
			}
		}

		if($opened > $closed){
			$string .= str_repeat("</span>", $opened - $closed);
		}

		return $string;
	}

	/**
	 *
	 * Match versions in string
	 *
	 *
	 * @param $string string
	 *
	 *
	 * @return array
	 *
	*/
	public static function versions(string $string) : array {
		if(!preg_match_all('/((Beta )|b)?\d{1,2}\.\d{1,2}(\.(\d{1,2}|x))?/i', $string, $matches)){
			return [];
		}

		return array_unique($matches[0]);
	}
}

?>
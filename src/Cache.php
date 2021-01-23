<?php

/**
 * Cache class of MCServerInfo
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

class Cache {

	private static $cache_status = false;

	private static $cache_dir = '/tmp';

	private static $cache_expire = 30;

	private static $data = [];

	public static function setStatus(bool $status) {
		self::$cache_status = $status;
	}

	public static function getStatus() : bool {
		return self::$cache_status;
	}

	public static function setDirectory(string $path) {
		self::$cache_dir = $path;
	}

	public static function getDirectory() : string {
		return self::$cache_dir;
	}

	public static function setExpire(int $seconds) {
		self::$cache_expire = $seconds;
	}

	public static function getExpire() : int {
		return self::$cache_expire;
	}

	public static function getData() : array {
		return self::$data;
	}

	private static function keygen($data) : string {
		return md5(var_export($data, true));
	}

	public static function get(string $host, int $port = 25565, string $method = 'AUTO') : ?array {
		$key = self::keygen([$host, $port, $method]);

		if(isset(self::$data[$key])){
			return self::$data[$key];
		}

		$filename = self::getDirectory()."/{$key}.php";

		if(!is_file($filename)){
			return null;
		}

		self::$data[$key] = (include($filename));

		if(self::$data[$key]['expire'] < time()){
			unset(self::$data[$key]);

			@unlink($filename);

			return null;
		}

		return self::$data[$key];
	}

	public static function set(string $host, int $port = 25565, string $method = 'AUTO', array $data = []) {
		$key = self::keygen([$host, $port, $method]);

		if(!is_dir(self::getDirectory())){
			@mkdir(self::getDirectory(), 0777, true);
		}

		$data['expire'] = time() + self::getExpire();
		$data['method'] = $method;

		$filename = self::getDirectory()."/{$key}.php";

		$str = "<?php".PHP_EOL.PHP_EOL;
		$str .= "return ".var_export($data, true).";".PHP_EOL.PHP_EOL;
		$str .= "?>";

		@file_put_contents($filename, $str, LOCK_EX);

		self::$data[$key] = $data;
	}
}

?>
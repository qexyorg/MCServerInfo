<?php

/**
 * Main class of MCServerInfo
 *
 * @author Qexy admin@qexy.org
 *
 * @package qexyorg\MCServerInfo
 *
 * @license MIT
 *
 * @version 1.1.0
*/

namespace qexyorg\MCServerInfo;

class MCServerInfo {

	private $storage = [];

	private function getConnect($params){
		$token = md5(var_export($params, true));

		return (isset($this->storage[$token])) ? $this->storage[$token] : null;
	}

	private function setConnect($params, $connect){
		$token = md5(var_export($params, true));

		$this->storage[$token] = $connect;

		return $this->storage[$token];
	}

	/**
	 * Connect to server
	 *
	 * @param $ip string
	 * @param $port integer
	 * @param $logic string
	 * @param $timeout integer
	 *
	 * @return MCServerInfoConnect
	*/
	public function connect($ip='127.0.0.1', $port=25565, $logic='', $timeout=3){
		$ip = strtolower($ip);

		$logic = strtolower($logic);

		$connect = $this->getConnect([$ip, $port, $logic]);

		if(!is_null($connect) && !$connect->getErrno()){
			return $connect;
		}

		$connect = new MCServerInfoConnect($ip, $port, $logic, $timeout);

		return $this->setConnect([$ip, $port, $logic], $connect);
	}

	public function removeFormatting($string){
		return preg_replace('/\§([0-9a-f]|k|l|m|n|o|r)/i', '', $string);
	}
}

?>
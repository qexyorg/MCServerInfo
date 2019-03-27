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
 * @version 1.0.0
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
	 * @param $old boolean
	 * @param $timeout integer
	 *
	 * @return MCServerInfoConnect
	*/
	public function connect($ip='127.0.0.1', $port=25565, $old=false, $timeout=3){
		$connect = $this->getConnect([$ip, $port, $old]);

		if(!is_null($connect) && !$connect->getErrno()){
			return $connect;
		}

		$connect = new MCServerInfoConnect($ip, $port, $old, $timeout);

		return $this->setConnect([$ip, $port, $old], $connect);
	}
}

?>
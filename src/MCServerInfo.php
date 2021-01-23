<?php

/**
 *
 * Main class of MCServerInfo
 *
 *
 * @author Qexy admin@qexy.org
 *
 *
 * @package qexyorg\MCServerInfo
 *
 *
 * @license MIT
 *
 *
 * @version 2.0.0
 *
 */

namespace qexyorg\MCServerInfo;

use qexyorg\MCServerInfo\Methods\Query;
use qexyorg\MCServerInfo\Methods\Ping;
use qexyorg\MCServerInfo\Methods\PingOld;

class MCServerInfo {


	/**
	 * Automatic search logic
	 * WARNING!!! Can be slow
	 * Queue steps: Query, Ping, PingOld
	*/
	const METHOD_AUTO = -1;


	const METHOD_PING = 0;


	const METHOD_QUERY = 1;


	const METHOD_OLD_PING = 2;


	/**
	 *
	 * Make new instance of MCServerInfoConnect
	 *
	 *
	 * @param $address string
	 *
	 *
	 * @param $port int
	 *
	 *
	 * @param $timeout int
	 *
	 *
	 * @return Connect
	 *
	*/
	public static function Connect(string $address = '127.0.0.1', int $port = 25565, int $timeout = 3) : Connect {
		return new Connect($address, $port, $timeout);
	}


	/**
	 * Method for make new instance of Query
	 *
	 * @param $connect Connect
	 *
	 * @return Query
	*/
	public static function Query(Connect $connect) : Query {
		return new Query($connect);
	}


	/**
	 * Method for make new instance of Ping
	 *
	 * @param $connect Connect
	 *
	 * @return Ping
	 */
	public static function Ping(Connect $connect) : Ping {
		return new Ping($connect);
	}


	/**
	 * Method for make new instance of PingOld
	 *
	 * @param $connect Connect
	 *
	 * @return PingOld
	 */
	public static function PingOld(Connect $connect) : PingOld {
		return new PingOld($connect);
	}
}

?>
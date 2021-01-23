<?php

/**
 * Old ping method class of MCServerInfo
 *
 * @author Qexy admin@qexy.org
 *
 * @package qexyorg\MCServerInfo
 *
 * @license MIT
 *
 * @version 1.0.0
 */

namespace qexyorg\MCServerInfo\Methods;

use qexyorg\MCServerInfo\Cache;
use qexyorg\MCServerInfo\Connect;
use qexyorg\MCServerInfo\Formatter;
use qexyorg\MCServerInfo\Response;

class PingOld extends Response implements MethodInterface {
	const METHOD_NAME = 'PING_OLD';

	private $connect, $socket;

	private $data = '';

	private $error = '';

	public function __construct(Connect $connect) {
		$this->connect = $connect;
	}

	public function getData() : string {
		return $this->data;
	}

	public function request() : bool {

		if(Cache::getStatus()){
			$cache = Cache::get($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME);

			if(!is_null($cache)){
				if(isset($cache['error'])){
					$this->error = $cache['error'];

					return false;
				}

				return true;
			}
		}

		$this->socket = @stream_socket_client("tcp://{$this->connect->getAddress()}:{$this->connect->getPort()}", $errno, $error, $this->connect->getTimeout());

		if($errno || $this->socket === false) {
			$error = @iconv('Windows-1251', "UTF-8//TRANSLIT", $error);

			$this->error = "[MCServerInfo] [".__LINE__."] Error: {$error} ({$errno})";

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			return false;
		}

		@stream_set_timeout($this->socket, $this->connect->getTimeout());

		/**
		 * For < 1.7
		 *
		 * @see https://wiki.vg/Server_List_Ping#1.6
		*/

		$query = pack('c*', 0xFE, 0x01);

		if(@fwrite($this->socket, $query, strlen($query)) === false){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			$this->socket = null;

			return false;
		}

		return true;
	}


	public function read() : bool {

		if(Cache::getStatus()){
			$cache = Cache::get($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME);

			if(!is_null($cache)){
				if(isset($cache['error'])){
					$this->error = $cache['error'];

					return false;
				}

				$this->status = $cache['status'];

				return true;
			}
		}

		if(is_null($this->socket)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: request not initialized';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$data = @fread($this->socket, 1024);

		if($data === false || empty($data)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to read socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$this->status = true;

		$this->data = substr($data, 3);

		return true;
	}


	public function parse() : MethodInterface {
		if(Cache::getStatus()){
			$cache = Cache::get($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME);

			if(!is_null($cache)){

				if(isset($cache['error'])){
					$this->error = $cache['error'];

					return $this;
				}

				$this->data = @$cache['response'];

				$this->import($cache);

				return $this;
			}
		}

		$this->data = iconv('UTF-16BE', 'UTF-8', $this->data);

		if($this->data[2] == "\x31"){
			/**
			 * For < 1.6
			 *
			 * @see https://wiki.vg/Server_List_Ping#1.4_to_1.5
			 */

			$this->data = explode("\x00", $this->data);

			$this->servername = !is_string(@$this->data[3]) ? "" : $this->data[3];

			$this->versions = isset($this->data[2]) ? Formatter::versions($this->data[2]) : [];

			$this->online = intval(@$this->data[4]);

			$this->slots = intval(@$this->data[5]);

		}else{
			/**
			 * For < 1.7
			 *
			 * @see https://wiki.vg/Server_List_Ping#1.6
			 */

			$this->data = explode("\xC2\xA7", $this->data); // correct for 1.6.1

			$this->servername = !is_string(@$this->data[0]) ? "" : $this->data[0];

			$this->online = intval(@$this->data[1]);

			$this->slots = intval(@$this->data[2]);

			$this->versions = Formatter::versions($this->servername);

			if(empty($this->versions)){ $this->versions = ["1.6.x"]; }
		}

		if(Cache::getStatus()){
			$save = $this->rawData();
			$save['response'] = $this->data;

			Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, $save);
		}

		return $this;
	}

	public function getError() : string {
		return $this->error;
	}
}

?>
<?php

/**
 * Query method class of MCServerInfo
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

class Query extends Response implements MethodInterface {
	const METHOD_NAME = 'QUERY';

	private $connect, $socket;

	private $data = '';

	private $error = '';

	private $lastpacket = '';

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

		$this->socket = @stream_socket_client("udp://{$this->connect->getAddress()}:{$this->connect->getPort()}", $errno, $error, $this->connect->getTimeout());

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
		 * Auth session
		 *
		 * @see https://github.com/SpigotMC/BungeeCord/blob/master/query/src/main/java/net/md_5/bungee/query/QueryHandler.java#L63
		*/

		$endquery = pack('c*', 0x00, 0x00, 0x00, 0x00);

		$query = pack('c*', 0xFE, 0xFD, 0x09).$endquery;

		$length = strlen($query);

		if(@fwrite($this->socket, $query, $length) !== $length){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$session = @fread($this->socket, 32);

		if($session === false){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to read socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$meta = stream_get_meta_data($this->socket);

		if($meta['timed_out']){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: connection timed out';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$query = pack('c*', 0xFE, 0xFD, 0x00).$endquery.pack('N', substr($session, 5)).$endquery;

		$length = strlen($query);

		if(@fwrite($this->socket, $query, $length) === false){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$data = @fread($this->socket, 2048);

		if($data === false){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to read socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$this->lastpacket = pack('N', substr($data, 5));

		$this->lastpacket = pack('c*', 0xFE, 0xFD, 0x09, 0x01, 0x01, 0x01, 0x01).$this->lastpacket;
		$this->lastpacket .= pack('c*', 0, 0, 0, 0);

		$length = strlen($this->lastpacket);

		if($length !== @fwrite($this->socket, $this->lastpacket, $length)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket ';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$this->data = $data;

		return true;
	}


	public function read() : bool {

		if(Cache::getStatus()){
			$cache = Cache::get($this->connect->getAddress(), $this->connect->getPort());

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

		$data = @fread($this->socket, 4096);

		if($data === false || empty($data)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to read socket ';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		if($data[0] != $this->lastpacket[2]){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket ';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$this->status = true;

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

		if(is_null($this->data)){
			return $this;
		}

		$data = substr($this->data, 16);

		$split = explode("\x00\x00\x01player_\x00\x00", $data);

		$split[1] = isset($split[1]) ? mb_substr($split[1], 0, -1) : "";

		$this->players = $split[1] != '' ? explode("\x00", mb_substr($split[1], 0, -1)) : [];

		$params = explode("\x00", $split[0]);

		$this->data = [];

		foreach($params as $k => $v){
			if($k % 2 != 0){
				$this->data[$params[$k - 1]] = mb_convert_encoding($v, 'UTF-8');
			}
		}

		if(isset($this->data['plugins'])){
			$this->data['plugins'] = explode(': ', $this->data['plugins']);

			if(isset($this->data['plugins'][0])){
				$this->software = $this->data['plugins'][0];

				unset($this->data['plugins'][0]);
			}

			$this->plugins = $this->data['plugins'];
		}

		if(isset($this->data['version'])){
			$this->versions = Formatter::versions($this->data['version']);
		}

		if(isset($this->data['hostname'])){
			$this->servername = $this->data['hostname'];
		}

		if(isset($this->data['numplayers'])){
			$this->online = intval($this->data['numplayers']);
		}

		if(isset($this->data['maxplayers'])){
			$this->slots = intval($this->data['maxplayers']);
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
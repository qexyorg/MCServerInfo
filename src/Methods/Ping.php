<?php

/**
 * Ping method class of MCServerInfo
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

class Ping extends Response implements MethodInterface {
	const METHOD_NAME = 'PING';

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

		$query = pack('c*', 0x00, 0x04);
		$query .= pack('c', strlen($this->connect->getAddress())).$this->connect->getAddress();
		$query .= pack('n', $this->connect->getPort())."\x01";
		$query = pack('c', strlen($query)).$query;

		if(@fwrite($this->socket, $query) === false || @fwrite($this->socket, "\x01\x00") === false){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: failed to write socket';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

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

		$size = $this->connect->size($this->socket);

		if(!$size){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: server returned invalid data. Maybe connection method is wrong';

			fclose($this->socket);

			return false;
		}

		$response = "";

		while(true){
			$length = strlen($response);

			if($length >= $size){ break; }

			$response .= @fread($this->socket, $size - $length);
		}

		if(empty($response)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: invalid response';

			if(Cache::getStatus()){
				Cache::set($this->connect->getAddress(), $this->connect->getPort(), self::METHOD_NAME, ['error' => $this->error]);
			}

			fclose($this->socket);

			return false;
		}

		$this->data = strstr($response, "{");

		if(empty($this->data)){
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: invalid response';

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

		$data = json_decode($this->data, true);

		if(isset($data['description'])){
			if(is_string($data['description'])){
				$this->description = $data['description'];
			}elseif(is_array($data['description'])){
				if(isset($data['description']['text'])){
					$this->servername = @strval($data['description']['text']);
				}

				if(isset($data['description']['text'])){
					$this->servername = @strval($data['description']['text']);
				}

				if(isset($data['description']['extra']) && is_array($data['description']['extra'])){
					$this->description = Formatter::parseExtra($data['description']['extra']);
				}
			}
		}

		if(isset($data['version'])){
			if(is_string($data['version'])){
				$this->versions = Formatter::versions($data['version']);
			}elseif(is_array($data['version'])){
				if(isset($data['version']['name'])){
					$this->software = $data['version']['name'];

					$this->versions = Formatter::versions($data['version']['name']);
				}

				if(isset($data['version']['protocol'])){
					$this->protocol = $data['version']['protocol'];
				}
			}
		}

		if(isset($data['players']) && is_array($data['players'])){
			if(isset($data['players']['online'])){
				$this->online = intval($data['players']['online']);
			}

			if(isset($data['players']['max'])){
				$this->slots = intval($data['players']['max']);
			}

			if(isset($data['players']['sample'])){
				$this->players = @array_column($data['players']['sample'], 'name');
			}
		}

		if(isset($data['modinfo']) && is_array($data['modinfo']) && isset($data['modinfo']['modList']) && is_array($data['modinfo']['modList'])){
			$this->mods = @array_column($data['modinfo']['modList'], 'modid');
		}

		if(isset($data['favicon'])){
			$this->favicon = $data['favicon'];
		}

		$this->data = $data;

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
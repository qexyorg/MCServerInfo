<?php

/**
 * Connect class of MCServerInfo
 *
 * @author Qexy admin@qexy.org
 *
 * @package qexyorg\MCServerInfo
 *
 * @license MIT
 *
 * @version 2.0.0
 */

namespace qexyorg\MCServerInfo;

use qexyorg\MCServerInfo\Methods\Query;
use qexyorg\MCServerInfo\Methods\Ping;
use qexyorg\MCServerInfo\Methods\PingOld;

class Connect {
	private $address = '127.0.0.1';

	private $port = 25565;

	private $timeout = 3;

	private $ipv4 = false;

	private $ip, $response;

	private $error = '';

	private $method = -1;


	/**
	 * Constructor with input param's
	 *
	 * @param $address string
	 *
	 * @param $port int
	 *
	 * @param $timeout int
	 *
	*/
	public function __construct(string $address = '127.0.0.1', int $port = 25565, int $timeout = 3) {
		$this->setAddress($address)
			->setPort($port)
			->setTimeout($timeout);
	}


	/**
	 * Enable/Disable cache
	 *
	 * @param $status bool
	 *
	 * @return self
	 */
	public function setCacheStatus(bool $status) : self {
		Cache::setStatus($status);

		return $this;
	}


	/**
	 * Getter for cache status
	 *
	 * @return bool
	 */
	public function getCacheStatus() : bool {
		return Cache::getStatus();
	}


	/**
	 * Set cache directory
	 *
	 * Default value: null
	 *
	 * @param $directory string
	 *
	 * @return self
	 */
	public function setCacheDirectory(string $directory) : self {
		Cache::setDirectory($directory);

		return $this;
	}


	/**
	 * Getter for cache directory
	 *
	 * @return string
	 */
	public function getCacheDirectory() : string {
		return Cache::getDirectory();
	}


	/**
	 * Set expire cache time in seconds
	 *
	 * Default value: 30
	 *
	 * @param $seconds int
	 *
	 * @return self
	 */
	public function setCacheExpire(int $seconds) : self {
		Cache::setExpire($seconds);

		return $this;
	}


	/**
	 * Getter for cache expire time
	 *
	 * @return int
	 */
	public function getCacheExpire() : int {
		return Cache::getExpire();
	}


	/**
	 * Setter for server ip or domain address
	 *
	 * @param $address string
	 *
	 * @return self
	*/
	public function setAddress(string $address) : self {
		$this->address = $address;

		return $this;
	}


	/**
	 * Getter for server address
	 *
	 * @return string
	*/
	public function getAddress() : string {
		return $this->address;
	}


	/**
	 * Setter for server port
	 *
	 * @param $port int
	 *
	 * @return self
	 */
	public function setPort(int $port) : self {
		$this->port = $port;

		return $this;
	}


	/**
	 * Getter for server port
	 *
	 * @return int
	 */
	public function getPort() : int {
		return $this->port;
	}


	/**
	 * Setter for connection timeout
	 *
	 * @param $seconds int
	 *
	 * @return self
	 */
	public function setTimeout(int $seconds) : self {
		$this->timeout = $seconds;

		return $this;
	}


	/**
	 * Getter for connection timeout
	 *
	 * @return int
	 */
	public function getTimeout() : int {
		return $this->timeout;
	}


	/**
	 * Return response object
	 *
	 * @see Query
	 * @see Ping
	 * @see PingOld
	 *
	 * @return Query | Ping | PingOld
	*/
	public function getResponse() : object {
		return $this->response;
	}


	/**
	 * Select connection method
	 *
	 * Supported values:
	 * @see MCServerInfo::METHOD_AUTO
	 * @see MCServerInfo::METHOD_PING
	 * @see MCServerInfo::METHOD_QUERY
	 * @see MCServerInfo::METHOD_OLD_PING
	 *
	 * Default value:
	 * @see MCServerInfo::METHOD_AUTO
	 *
	 * @param $method int
	 *
	 * @return self
	*/
	public function setMethod(int $method) : self {
		$this->method = $method;

		return $this;
	}


	/**
	 * Getter for connection method
	 *
	 * @return string
	*/
	public function getMethod() : string {
		return $this->method;
	}


	/**
	 * Use IPv4 address for connection with server
	 *
	 * @param $status bool
	 *
	 * @return self
	*/
	public function IPv4(bool $status) : self {
		$this->ipv4 = $status;

		return $this;
	}


	/**
	 * Getter for IPv4 address
	 *
	 * @return string | null
	*/
	public function getIPv4() : ?string {
		return $this->ip;
	}


	/**
	 * Getter for error
	 *
	 * @return string
	*/
	public function getError() : string {
		return $this->error;
	}


	/**
	 * Request connect to the server
	 *
	 * @return bool
	*/
	public function request() : bool {

		if($this->ipv4){

			if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/i", $this->getAddress())){
				$this->ip = $this->getAddress();
			}else{
				$ip = @gethostbyname($this->getAddress());

				if($ip == $this->getAddress()){
					$this->error = '[MCServerInfo] ['.__LINE__.'] Error: invalid server IPv4 address';

					return false;
				}else{
					$this->ip = $ip;
				}
			}
		}

		if($this->method == MCServerInfo::METHOD_PING){

			$method = MCServerInfo::Ping($this);

		}elseif($this->method == MCServerInfo::METHOD_QUERY){

			$method = MCServerInfo::Query($this);

		}elseif($this->method == MCServerInfo::METHOD_OLD_PING){

			$method = MCServerInfo::PingOld($this);

		}elseif($this->method == MCServerInfo::METHOD_AUTO){

			$method = MCServerInfo::Query($this);

			if($method->request() && $method->read()){
				$this->response = $method->parse();

				return true;
			}

			$method = MCServerInfo::Ping($this);

			if($method->request() && $method->read()){
				$this->response = $method->parse();

				return true;
			}

			$method = MCServerInfo::PingOld($this);

			if($method->request() && $method->read()){
				$this->response = $method->parse();

				return true;
			}

			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: no one method can\'t connect to server';

			return false;
		}else{
			$this->error = '[MCServerInfo] ['.__LINE__.'] Error: invalid connect method';

			return false;
		}

		if(!$method->request() || !$method->read()){
			$this->error = $method->getError();

			return false;
		}

		$this->response = $method->parse();

		return true;
	}


	/**
	 * Request stream socket length
	 *
	 * @param $socket
	 *
	 * @return int
	*/
	public function size($socket) : int {
		$size = 0;

		$i = 0;

		while(true){
			$read = @fgetc($socket);

			if($read === false){ break; }

			$read = ord($read);

			$size |= ($read & 0x7F) << $i++ * 7;

			if($i > 5){ $this->error = '[MCServerInfo] ['.__LINE__.'] Error: To many steps'; break; }

			if(($read & 0x80) != 128){ break; }
		}

		return $size;
	}
}

?>
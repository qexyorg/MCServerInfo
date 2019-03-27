<?php

/**
 * Logic class of MCServerInfo
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

class MCServerInfoConnect {
	const ERROR_IP_FORMAT = 1;
	const ERROR_ADDRESS_FORMAT = 2;
	const ERROR_SOCKET = 3;
	const ERROR_SOCKET_LENGTH = 4;
	const ERROR_HANDSHAKE = 5;
	const ERROR_STATUS_PING = 6;
	const ERROR_READ_TIMEOUT = 7;
	const ERROR_WRITE_SOCKET = 8;
	const ERROR_READ_SOCKET = 9;
	const ERROR_SERVER_PARSE = 10;
	const ERROR_READ_DATA = 11;

	private $old = false;
	private $ip = '127.0.0.1';
	private $port = 25565;
	private $address = '127.0.0.1';
	private $error = '';
	private $errno = 0;
	private $socket = null;
	private $timeout = 3;
	private $logic = ''; // query | ping | ping_old

	private $version = '';
	private $protocol = '';

	private $status = false;
	private $online = 0;
	private $slots = 0;
	private $players = [];
	private $servername = '';
	private $favicon = '';
	private $mods = [];
	private $plugins = [];

	public function __construct($ip, $port=25565, $old=false, $timeout=3){
		$this->setIP($ip)
			->setAddress($ip)
			->setPort($port)
			->setOld($old);
	}

	/**
	 * Is old version getter
	 *
	 * @return boolean
	 */
	public function getOld(){
		return $this->old;
	}

	/**
	 * IP address getter
	 *
	 * @return string
	 */
	public function getIP(){
		return $this->ip;
	}

	/**
	 * Logic getter
	 *
	 * @return string
	 */
	public function getLogic(){
		return $this->logic;
	}

	/**
	 * Logic setter
	 *
	 * @param $logic string
	 *
	 * @return $this
	 */
	public function setLogic($logic){
		$this->logic = $logic;

		return $this;
	}

	/**
	 * Port getter
	 *
	 * @return integer
	 */
	public function getPort(){
		return $this->port;
	}

	/**
	 * Address getter
	 *
	 * @return string
	 */
	public function getAddress(){
		return $this->address;
	}

	/**
	 * Is old setter
	 *
	 * @param $value boolean
	 *
	 * @return $this
	 */
	public function setOld($value){
		$this->old = ($value===true);
		return $this;
	}

	/**
	 * Error setter
	 *
	 * @param $error string
	 * @param $errno integer|null
	 *
	 * @return $this
	 */
	protected function setError($error, $errno=null){
		$this->error = $error;

		if(!is_null($errno)){
			$this->setErrno($errno);
		}

		return $this;
	}

	/**
	 * Error getter
	 *
	 * @return string
	 */
	public function getError(){
		return $this->error;
	}

	/**
	 * Errno setter
	 *
	 * @param $errno integer
	 *
	 * @return $this
	 */
	protected function setErrno($errno){
		$this->errno = intval($errno);

		return $this;
	}

	/**
	 * Errno getter
	 *
	 * @return integer
	 */
	public function getErrno(){
		return $this->errno;
	}

	/**
	 * IP setter
	 *
	 * @param $ip string
	 *
	 * @throws MCServerInfoException
	 *
	 * @return $this
	 */
	public function setIP($ip){
		$ip = strtolower($ip);

		if(preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/i', $ip)){
			$this->ip = $ip;

			return $this;
		}

		if(!preg_match('/^([a-z0-9\-\.]{1,255})$/i', $ip)){
			$this->setError('Invalid IP format', self::ERROR_IP_FORMAT);

			throw new MCServerInfoException($this->getError());
		}

		$this->ip = gethostbyname($ip);

		return $this;
	}

	/**
	 * Port setter
	 *
	 * @param $port integer
	 *
	 * @return $this
	 */
	public function setPort($port){
		$this->port = intval($port);

		if($this->port<=0){
			$this->port = 25565;
		}

		return $this;
	}

	/**
	 * Address setter
	 *
	 * @param $address string
	 *
	 * @throws MCServerInfoException
	 *
	 * @return $this
	 */
	public function setAddress($address){
		if(!preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})|([a-z0-9\-\.]{1,255})$/i', $address)){
			$this->setError('Invalid address format', self::ERROR_ADDRESS_FORMAT);

			throw new MCServerInfoException($this->getError());
		}

		$address = strtolower($address);

		$this->address = $address;

		return $this;
	}

	/**
	 * Socket setter
	 *
	 * @param $socket resource|null
	 *
	 * @return $this
	 */
	protected function setSocket($socket){

		if(is_null($this->getSocket())){
			@fclose($this->getSocket());
		}

		$this->socket = $socket;

		return $this;
	}

	/**
	 * Socket getter
	 *
	 * @return resource|null
	 */
	protected function getSocket(){
		return $this->socket;
	}

	/**
	 * Status setter
	 *
	 * @param $status boolean
	 *
	 * @return $this
	 */
	public function setStatus($status){
		$this->status = ($status==true);

		return $this;
	}

	/**
	 * Status getter
	 *
	 * @return boolean
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Version setter
	 *
	 * @param $version string
	 *
	 * @return $this
	 */
	public function setVersion($version){
		$this->version = $version;

		return $this;
	}

	/**
	 * Version getter
	 *
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * Protocol setter
	 *
	 * @param $protocol string
	 *
	 * @return $this
	 */
	public function setProtocol($protocol){
		$this->protocol = $protocol;

		return $this;
	}

	/**
	 * Protocol getter
	 *
	 * @return string
	 */
	public function getProtocol(){
		return $this->protocol;
	}

	/**
	 * Online setter
	 *
	 * @param $online integer
	 *
	 * @return $this
	 */
	public function setOnline($online){
		$this->online = intval($online);

		return $this;
	}

	/**
	 * Online getter
	 *
	 * @return integer
	 */
	public function getOnline(){
		return $this->online;
	}

	/**
	 * Slots setter
	 *
	 * @param $slots integer
	 *
	 * @return $this
	 */
	public function setSlots($slots){
		$this->slots = intval($slots);

		return $this;
	}

	/**
	 * Slots getter
	 *
	 * @return integer
	 */
	public function getSlots(){
		return $this->slots;
	}

	/**
	 * Server name setter
	 *
	 * @param $name string
	 *
	 * @return $this
	 */
	public function setServerName($name){
		$this->servername = $name;

		return $this;
	}

	/**
	 * Server name getter
	 *
	 * @return string
	 */
	public function getServerName(){
		return $this->servername;
	}

	/**
	 * Favicon setter
	 *
	 * @param $favicon string
	 *
	 * @return $this
	 */
	public function setFavicon($favicon){
		$this->favicon = $favicon;

		return $this;
	}

	/**
	 * Favicon getter
	 *
	 * @return string
	 */
	public function getFavicon(){
		return $this->favicon;
	}

	/**
	 * Players setter
	 *
	 * @param $players array
	 *
	 * @return $this
	 */
	public function setPlayers($players){
		$this->players = $players;

		return $this;
	}

	/**
	 * Players getter
	 *
	 * @return array
	 */
	public function getPlayers(){
		return $this->players;
	}

	/**
	 * Mods setter
	 *
	 * @param $mods array
	 *
	 * @return $this
	 */
	public function setMods($mods){
		$this->mods = $mods;

		return $this;
	}

	/**
	 * Mods getter
	 *
	 * @return array
	 */
	public function getMods(){
		return $this->mods;
	}

	/**
	 * Plugins setter
	 *
	 * @param $plugins array
	 *
	 * @return $this
	 */
	public function setPlugins($plugins){
		$this->plugins = $plugins;

		return $this;
	}

	/**
	 * Plugins getter
	 *
	 * @return array
	 */
	public function getPlugins(){
		return $this->plugins;
	}

	/**
	 * Timeout setter
	 *
	 * @param $timeout integer
	 *
	 * @return $this
	 */
	public function setTimeout($timeout){
		$this->timeout = intval($timeout);

		if($this->timeout<=0){
			$this->timeout = 3;
		}

		return $this;
	}

	/**
	 * Timeout getter
	 *
	 * @return integer
	 */
	public function getTimeout(){
		return $this->timeout;
	}

	public function clear(){
		$this->setOld(false)
			->setPort(25565)
			->setAddress('127.0.0.1')
			->setIP('127.0.0.1')
			->setErrno(0)
			->setError('')
			->setStatus(false)
			->setFavicon('')
			->setMods([])
			->setPlayers([])
			->setOnline(0)
			->setSlots(0)
			->setProtocol('')
			->setServerName('')
			->setSocket(null)
			->setVersion('');
	}

	public function disconnect(){
		$this->setSocket(null);
	}

	public function query($protocol='udp'){
		$socket = @fsockopen("{$protocol}://".$this->getAddress(), $this->getPort(), $errno, $error, $this->getTimeout());

		if($errno || $socket===false){
			$this->setError("{$error} #{$errno}", self::ERROR_SOCKET);

			return false;
		}

		$this->setSocket($socket);

		@stream_set_timeout($this->getSocket(), $this->getTimeout());

		return true;
	}

	public function ping(){
		return $this->query('tcp');
	}

	private function write($command, $after=''){
		$command = pack('c*', 0xFE, 0xFD, $command, 0x01, 0x02, 0x03, 0x04).$after;
		$len  = strlen($command);

		if($len !== @fwrite($this->getSocket(), $command, $len)){
			$this->setError('Error write socket', self::ERROR_WRITE_SOCKET);

			return false;
		}

		$data = @fread($this->getSocket(), 4096);

		if($data === false){
			$this->setError('Error read socket', self::ERROR_READ_SOCKET);

			return false;
		}

		if(strlen($data) < 5 || $data[0] != $command[2]){
			return false;
		}

		return substr($data, 5);
	}

	public function startQueryLogic(){

		$data = $this->write(0x09);

		if($data===false){
			return false;
		}

		$data = pack('N', $data);

		$data = $this->write(0x00, $data.Pack('c*', 0x00, 0x00, 0x00, 0x00));

		if(!$data){
			return false;
		}

		$data = substr($data, 11);
		$data = explode("\x00\x00\x01player_\x00\x00", $data);

		if(count($data) !== 2){
			$this->setError('Error parse response', self::ERROR_SERVER_PARSE);

			return false;
		}

		$this->setStatus(true);

		$players = substr($data[1], 0, -2);
		$data = explode("\x00", $data[0]);

		$key = $value = '';

		foreach($data as $k => $v){
			if($k % 2 == 0){
				$key = $v;

				continue;
			}

			switch($key){
				case 'hostname': $this->setServerName($v); break;
				case 'version': $this->setVersion($v); break;
				case 'plugins':
					$v = explode(': ', $v);

					if(isset($v[1])){
						$this->setPlugins(explode('; ', $v[1]));
					}
				break;
				case 'numplayers': $this->setOnline($v); break;
				case 'maxplayers': $this->setSlots($v); break;
			}
		}

		if(!empty($players)){
			$this->setPlayers(explode("\x00", $players));
		}

		$this->setLogic('query');

		return true;
	}

	public function startPingLogic(){

		if(!$this->runPing()){
			return $this->runPingOld();
		}

		return true;
	}

	public function runPingOld(){
		if(!@fwrite($this->getSocket(), "\xFE\x01")){
			$this->setError("Error query ping old write socket", self::ERROR_WRITE_SOCKET);

			return false;
		}

		$data = @fread($this->getSocket(), 512);

		if(!$data){
			$this->setError("Error query ping old second write socket", self::ERROR_WRITE_SOCKET);

			return false;
		}

		$len = strlen($data);

		if($len < 4 || $data[0] !== "\xFF"){
			$this->setError('Error read query int length', self::ERROR_SOCKET_LENGTH);

			return false;
		}

		$this->setLogic('ping_old');

		$data = substr($data, 3);

		$data = iconv('UTF-16BE', 'UTF-8', $data);

		if($data[1] === "\xA7" && $data[2] === "\x31"){
			$data = explode("\x00", $data);

			$this->setProtocol(@$data[1])
				->setVersion(@$data[2])
				->setServerName(@$data[3])
				->setOnline(@$data[4])
				->setSlots(@$data[5]);

			return true;
		}

		$data = explode("\xA7", $data);

		$this->setProtocol(0)
			->setVersion('1.3')
			->setServerName(@substr($data[0], 0, -1))
			->setOnline(@$data[1])
			->setSlots(@$data[2]);

		return true;
	}

	private function runPing(){
		$start = microtime(true);

		$data = "\x00\x04";
		$data .= pack('c', strlen($this->getIP())).$this->getIP();
		$data .= pack('n', $this->getPort())."\x01";

		$data = pack('c', strlen($data)).$data;

		if(!@fwrite($this->getSocket(), $data)){
			$this->setError("Error query ping write socket", self::ERROR_WRITE_SOCKET);

			return false;
		}

		if(!@fwrite($this->getSocket(), "\x01\x00")){
			$this->setError("Error query ping second write socket", self::ERROR_WRITE_SOCKET);

			return false;
		}

		$len = $this->readInt();

		if($len < 10){
			$this->setError('Error read query int length', self::ERROR_SOCKET_LENGTH);

			return false;
		}

		if(!@fgetc($this->getSocket())){
			$this->setError('Error fgetc', self::ERROR_SOCKET);

			return false;
		}

		$len = $this->readInt();

		$data = "";

		do {
			if(microtime(true) - $start > $this->getTimeout()){
				$this->setError('Read timeout', self::ERROR_READ_TIMEOUT);

				return false; break;
			}

			$block = @fread($this->getSocket(), $len - strlen($data));

			if(!$block){
				$this->setError('Read timeout', self::ERROR_READ_SOCKET);

				return false; break;
			}

			$data .= $block;
		} while(strlen($data) < $len);

		if($data === FALSE || empty($data)){
			$this->setError('Error read data', self::ERROR_READ_DATA);

			return false;
		}

		$data = json_decode($data, true);

		if($data===false){
			$this->setError('Error parse data', self::ERROR_SERVER_PARSE);

			return false;
		}

		$this->setVersion(@$data['version']['name'])
			->setProtocol(@$data['version']['protocol'])
			->setOnline(@$data['players']['online'])
			->setSlots(@$data['players']['max'])
			->setStatus(true);

		$this->setLogic('ping');

		return true;
	}

	public function searchLogic(){
		if(!$this->query() || !$this->startQueryLogic()){
			if(!$this->ping() || !$this->startPingLogic()){
				return false;
			}

			return true;
		}

		return true;
	}

	private function readInt(){
		$res = 0;
		$pre = 0;

		while(true){
			$read = @fgetc($this->getSocket());

			if($read===false){
				return 0;
			}

			$read = ord($read);

			$res |= ($read & 0x7F) << $pre++ * 7;

			if($pre > 5){
				$this->setError('Socket integer variable too big', self::ERROR_SOCKET_LENGTH);

				throw new MCServerInfoException($this->getError());
			}

			if(($read & 0x80) != 128){
				break;
			}
		}

		return $res;
	}

	public function getResponse(){
		return [
			'status' => $this->getStatus(),
			'error' => $this->getError(),
			'errno' => $this->getErrno(),
			'address' => $this->getAddress(),
			'ip' => $this->getIP(),
			'port' => $this->getPort(),
			'version' => $this->getVersion(),
			'protocol' => $this->getProtocol(),
			'online' => $this->getOnline(),
			'slots' => $this->getSlots(),
			'players' => $this->getPlayers(),
			'servername' => $this->getServerName(),
			'favicon' => $this->getFavicon(),
			'mods' => $this->getMods()
		];
	}

	public function execute(){
		return $this->searchLogic();
	}
}

?>
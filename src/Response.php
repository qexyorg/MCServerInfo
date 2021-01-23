<?php

/**
 * Response class of MCServerInfo
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

class Response {
	protected $status = false;

	protected $versions = [];

	protected $protocol = 0;

	protected $online = 0;

	protected $slots = 0;

	protected $servername = '';

	protected $description = '';

	protected $favicon = '';

	protected $software = '';

	protected $players = [];

	protected $motd = '';

	protected $plugins = [];

	protected $mods = [];

	public function import(array $data) : self {

		if(isset($data['status'])){
			$this->status = boolval($data['status']);
		}

		if(isset($data['online'])){
			$this->online = intval($data['online']);
		}

		if(isset($data['slots'])){
			$this->slots = intval($data['slots']);
		}

		if(isset($data['versions'])){
			$this->versions = $data['versions'];
		}

		if(isset($data['protocol'])){
			$this->protocol = $data['protocol'];
		}

		if(isset($data['servername'])){
			$this->servername = $data['servername'];
		}

		if(isset($data['description'])){
			$this->description = $data['description'];
		}

		if(isset($data['favicon'])){
			$this->favicon = $data['favicon'];
		}

		if(isset($data['software'])){
			$this->software = $data['software'];
		}

		if(isset($data['motd'])){
			$this->motd = $data['motd'];
		}

		if(isset($data['players'])){
			$this->players = $data['players'];
		}

		if(isset($data['plugins'])){
			$this->plugins = $data['plugins'];
		}

		if(isset($data['mods'])){
			$this->mods = $data['mods'];
		}

		return $this;
	}

	public function rawData() : array {
		return [
			'status' => $this->getStatus(),
			'online' => $this->getOnline(),
			'slots' => $this->getSlots(),
			'versions' => $this->getVersions(),
			'protocol' => $this->getProtocol(),
			'servername' => $this->getServername(),
			'description' => $this->getDescription(),
			'favicon' => $this->getFavicon(),
			'software' => $this->getSoftware(),
			'motd' => $this->getMotd(),
			'players' => $this->getPlayers(),
			'plugins' => $this->getPlugins(),
			'mods' => $this->getMods()
		];
	}

	public function getStatus() : bool {
		return $this->status;
	}

	public function getVersions() : array {
		return $this->versions;
	}

	public function getProtocol() : int {
		return $this->protocol;
	}

	public function getOnline() : int {
		return $this->online;
	}

	public function getSlots() : int {
		return $this->slots;
	}

	public function getServername() : string {
		return $this->servername;
	}

	public function getDescription() : string {
		return $this->description;
	}

	public function getFavicon() : string {
		return $this->favicon;
	}

	public function getSoftware() : string {
		return $this->software;
	}

	public function getMotd() : string {
		return $this->motd;
	}

	public function getPlayers() : array {
		return $this->players;
	}

	public function getPlugins() : array {
		return $this->plugins;
	}

	public function getMods() : array {
		return $this->mods;
	}

	/* Alternative getter method
	 *
	 * public function __call($name, $args) {

		$name = strtolower($name);

		if(substr($name, 0, 3) == 'get'){
			$name = substr($name, 3);

			if(isset($this->{$name})){
				return $this->{$name};
			}
		}

		return null;
	}*/
}

?>
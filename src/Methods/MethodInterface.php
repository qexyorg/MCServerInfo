<?php

/**
 * Method interface of MCServerInfo
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

use qexyorg\MCServerInfo\Connect;

interface MethodInterface {


	/**
	 * Getter for error message
	 *
	 * @return string
	*/
	public function getError() : string;


	/**
	 *
	 * Constructor method
	 *
	 *
	 * @param $connect Connect
	 *
	*/
	public function __construct(Connect $connect);


	/**
	 *
	 * Request to server via open stream socket
	 *
	 *
	 * @return bool
	 *
	*/
	public function request() : bool;


	/**
	 *
	 * Read socket data
	 *
	 *
	 * @return bool
	 *
	 */
	public function read() : bool;


	/**
	 *
	 * Get unparsed data
	 *
	 *
	 * @return string
	 *
	 */
	public function getData() : string;


	/**
	 *
	 * Parse response data
	 *
	 *
	 * @return self
	 *
	 */
	public function parse() : self;
}

?>
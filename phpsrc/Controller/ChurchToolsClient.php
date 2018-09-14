<?php

namespace StevenBuehner\Controller;

use StevenBuehner\Exceptions\RequestErrorException;

abstract class ChurchToolsClient {

	const MODULE_LOGIN    = 'login/ajax';
	const MODULE_CALENDAR = 'churchcal/ajax';
	const MODULE_RESOURCE = 'churchresource/ajax';

	protected $cookies    = [];
	protected $isLoggedIn = FALSE;
	protected $url;

	public function __construct($url) {
		$this->url = $url;

	}

	/**
	 * @param string $ctModule
	 * @param string $func
	 * @param array  $data
	 * @return mixed
	 * @throws RequestErrorException
	 */
	public function sendChurchToolsRequest($ctModule, $func, $data = []) {

		$url = $this->getQueryUrl($ctModule);

		return $this->sendRequest($url, $data, $func);
	}

	/**
	 * @param string $requestClass
	 * @return string
	 */
	protected function getQueryUrl($requestClass) {
		$url = $this->getUrl() . '/index.php?q=' . $requestClass;

		return $url;
	}

	/**
	 * @return string
	 */
	protected function getUrl() {
		return $this->url;
	}

	/**
	 * @param      $url
	 * @param      $data
	 * @param null $func
	 * @throws RequestErrorException
	 * @return mixed
	 */
	protected function sendRequest($url, $data, $func = NULL) {

		if (!empty($func)) {
			$data['func'] = $func;
		}

		$options = [
			'http' => [
				'header'  => "Cookie: " . $this->getCookies() . "\r\nContent-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			]
		];
		$context = stream_context_create($options);
		$result  = @file_get_contents($url, FALSE, $context);

		if ($result === FALSE) {
			throw new RequestErrorException();
		}

		$obj = json_decode($result);

		if ($obj->status == 'error') {
			throw new RequestErrorException($obj->message);
		}

		$this->saveCookies($http_response_header);

		return $obj;
	}

	/**
	 * @return string
	 */
	protected function getCookies() {
		$res = "";
		foreach ($this->cookies as $key => $cookie) {
			$res .= "$key=$cookie; ";
		}

		return $res;
	}

	/**
	 * @param array $r
	 */
	protected function saveCookies($r) {
		foreach ($r as $hdr) {
			if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
				parse_str($matches[1], $tmp);
				$this->cookies += $tmp;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isLoggedIn() {
		return $this->isLoggedIn;
	}


	public abstract function login();
}
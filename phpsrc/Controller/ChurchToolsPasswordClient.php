<?php

namespace StevenBuehner\Controller;

use StevenBuehner\Exceptions\InvalidLoginException;
use StevenBuehner\Exceptions\RequestErrorException;

class ChurchToolsPasswordClient extends ChurchToolsClient {


	protected $email;
	protected $password;

	public function __construct($url, $email, $password) {
		parent::__construct($url);

		$this->email    = $email;
		$this->password = $password;
	}

	/**
	 * @return bool
	 * @throws InvalidLoginException
	 */
	public function login() {

		// Login with email and password
		$url  = $this->getQueryUrl(self::MODULE_LOGIN);
		$data = [
			'email'    => $this->getEmail(),
			'password' => $this->getPassword()
		];

		try {
			$result = $this->sendRequest($url, $data, 'login');
		} catch (RequestErrorException $prev) {
			throw new InvalidLoginException('Invalid Login', 0, $prev);
		}


		if ($result === NULL) {
			$this->isLoggedIn = FALSE;
			throw new InvalidLoginException('Wrong URL or something like that');
		}

		if ($result->status == "fail") {
			$this->isLoggedIn = FALSE;

			throw new InvalidLoginException($result->data);
		} else {
			$this->isLoggedIn = TRUE;
		}

		return $this->isLoggedIn;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}


}
<?php

namespace StevenBuehner\Proxy {

	use StevenBuehner\Controller\ChurchToolsClient;
	use StevenBuehner\Exceptions\InvalidLoginException;
	use StevenBuehner\Exceptions\RequestErrorException;

	class ChurchToolsProxy {

		protected $client       = NULL;
		protected $allowedHosts = [];

		public function __construct(ChurchToolsClient $client, $allowedHosts = []) {
			$this->client       = $client;
			$this->allowedHosts = $allowedHosts;
		}

		public function run() {

			session_start();

			// Header
			$this->checkOrigin();
			$this->sendAdditionalHeaders();

			// Method: Option
			$this->checkOptionsMethod();

			$this->checkCachedClient();

			// Login if not logged in already
			$this->doLogin();

			list($module, $function, $data) = $this->getParameters();

			$this->checkRequestPermission($module, $function, $data);

			$this->runRequest($module, $function, $data);
		}

		protected function checkOrigin() {
			$origin = $_SERVER['HTTP_ORIGIN'];

			if (in_array($origin, $this->allowedHosts)) {
				header('Access-Control-Allow-Origin: ' . $origin);
			} else if (in_array('*', $this->allowedHosts)) {
				header('Access-Control-Allow-Origin: *');
			} else if ($origin === NULL) {
			} else {
				http_response_code(401);
				die('Unauthorized Origin');
			}

			return TRUE;
		}

		protected function sendAdditionalHeaders() {
			header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
			header('Access-Control-Allow-HEADERS: Content-Type, Origin, Accept');
			header('Content-Type: application/json');
		}

		protected function checkOptionsMethod() {
			// Access-Control headers are received during OPTIONS requests
			if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
				exit(0);
			}
		}

		protected function checkCachedClient() {
			if (isset($_SESSION['client']) && !is_null($_SESSION['client']) && $_SESSION['client'] instanceof ChurchToolsClient) {
				$this->client = $_SESSION['client'];
			}
		}

		protected function doLogin() {
			if (!$this->client->isLoggedIn()) {
				try {
					$this->client->login();
				} catch (InvalidLoginException $e) {

					// Unauthorized
					http_response_code(401);

					die(json_encode(
						['error' => $e->getMessage()]
					));

				}
			}
		}

		protected function getParameters() {

			if (!isset($_POST['m']) || !isset($_POST['f'])) {

				// Bad Request
				http_response_code(400);
				die(json_encode(
					['error' => 'Missing parameters']
				));
			}

			$module   = $_POST['m'];
			$function = $_POST['f'];
			$data     = (isset($_POST['data'])) ? $_POST['data'] : [];

			return [$module, $function, $data];

		}

		protected function checkRequestPermission($module, $function, $data) {
			// Todo: Implement permissions
			return TRUE;
		}

		protected function runRequest($module, $function, $data) {

			$statusCode = 200;
			$response   = [];

			try {
				$response = $this->client->sendChurchToolsRequest($module, $function, $data);

				if ($response->status == 'error') {

					// Internal Server Error
					$statusCode = 505;
					$response   = [
						'error' => $response->message
					];

				}


			} catch (RequestErrorException $e) {

				// Internal Server Error
				$statusCode = 505;
				$response   = [
					'error' => $e->getMessage()
				];
			}

			http_response_code($statusCode);
			die(json_encode($response));

		}


	}
}
<?php

namespace StevenBuehner\Exceptions;

use Throwable;

class RequestErrorException extends \Exception {

	public function __construct($message = "Request Error", $code = 0, Throwable $previous = NULL) {
		parent::__construct($message, $code, $previous);
	}

}
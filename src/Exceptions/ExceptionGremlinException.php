<?php
declare(strict_types=1);

namespace ChaosGremlin\Exceptions;

use Exception;

class ExceptionGremlinException extends Exception {
	/**
	 * Constructor
	 *
	 * @param string $message The exception message
	 */
	public function __construct(string $message = "Oh no, an exception gremlin was released!") {
		parent::__construct($message, 0, NULL);
	}
}
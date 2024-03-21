<?php
declare(strict_types=1);

namespace ChaosGremlin\Exceptions;

use Exception;

class ChaosGremlinInstanceException extends Exception {
	public function __construct(string $message = "I'm a singleton, you're in danger!") {
		parent::__construct($message, 0, null);
	}
}
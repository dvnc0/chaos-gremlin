<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

use ChaosGremlin\Exceptions\ExceptionGremlinException;

class Exception_Gremlin extends Gremlin {

	/**
	 * Attack the system by introducing a random exception
	 *
	 * @return void
	 */
	public function attack(): void {
		throw new ExceptionGremlinException($this->settings['exception_message']);
	}
}
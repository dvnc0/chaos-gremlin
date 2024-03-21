<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Die_Gremlin extends Gremlin {

	/**
	 * Attack the system by calling die
	 *
	 * @return void
	 */
	public function attack(): void {
		die();
	}
}
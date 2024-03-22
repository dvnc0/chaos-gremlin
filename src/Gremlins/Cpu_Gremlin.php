<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Cpu_Gremlin extends Gremlin {

	/**
	 * Attack the system by consuming CPU
	 *
	 * @return void
	 */
	public function attack(): void {

		if ($this->rollDice() === false) {
			return;
		}

		// this will consume CPU until the yes command is killed
		exec("yes > /dev/null &");
	}
}
<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Cpu_Gremlin extends Gremlin {

	/**
	 * Attack the system by consuming CPU
	 * 
	 * Runs until process is killed.
	 *
	 * @return void
	 */
	public function attack(): void {
		exec("yes > /dev/null &");
		$this->writeToLog('CPU Gremlin is attacking the system');
	}
}
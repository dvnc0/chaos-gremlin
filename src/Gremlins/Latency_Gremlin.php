<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Latency_Gremlin extends Gremlin {

	/**
	 * Attack the system by introducing latency
	 *
	 * @return void
	 */
	public function attack(): void {
		$latency = rand($this->settings['min_latency_seconds'], $this->settings['max_latency_seconds']);
		sleep($latency);
	}
}
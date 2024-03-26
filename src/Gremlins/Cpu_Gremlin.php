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
		$this->cpuNoMore();
		return;
	}

	/**
	 * Consume CPU
	 *
	 * @return void
	 */
	protected function cpuNoMore(): void {
		$pid = $this->getFork();
		if ($pid === -1) {
			die('Could not fork');
		} elseif ($pid) {
			$this->writeToLog('CPU Gremlin is attacking the system.');
			return;
		} else {
			$this->writeToLog('CPU Gremlin is using PID: ' . getmypid() . ' to attack the system.');
			exec("yes > /dev/null &");
			exit;
		}
	}
}
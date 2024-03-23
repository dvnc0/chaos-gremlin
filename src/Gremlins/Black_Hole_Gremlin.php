<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Black_Hole_Gremlin extends Gremlin {

	/**
	 * Attack the system by writing a random amount of data to /dev/null
	 *
	 * @return void
	 */
	public function attack(): void {
		$pid = pcntl_fork();
		if ($pid === -1) {
			$this->writeToLog('Black Hole Gremlin failed to fork');
			exit(1);
		} elseif ($pid) {
			$this->writeToLog('Black Hole Gremlin is attacking the system');
			return;
		} else {
			$black_hole = fopen('/dev/null', 'w');
			fwrite($black_hole, 'Black Hole Gremlin');
			$size = rand(1000000, 9999999999);
			$random_string = str_repeat('1', $size);
			fwrite($black_hole, $random_string);
			fclose($black_hole);
		}
	}
}
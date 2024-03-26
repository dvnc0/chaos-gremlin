<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

use ChaosGremlin\Traits\File_Helper_Trait;

class Black_Hole_Gremlin extends Gremlin {

	use File_Helper_Trait;

	/**
	 * Attack the system by writing a random amount of data to /dev/null
	 *
	 * @return void
	 */
	public function attack(): void {
		$this->writeToTheVoid();
		return;
	}

	/**
	 * Write to the void
	 *
	 * @return void
	 */
	protected function writeToTheVoid(): void {
		$pid = $this->getFork();
		if ($pid === -1) {
			$this->writeToLog('Black Hole Gremlin failed to fork');
			exit(1);
		} elseif ($pid) {
			$this->writeToLog('Black Hole Gremlin is attacking the system');
			return;
		} else {
			$black_hole = $this->fileOpen('/dev/null', 'w');
			$this->fileWrite($black_hole, 'Black Hole Gremlin');
			$size          = rand(1000000, 9999999999);
			$random_string = str_repeat('1', $size);
			$this->fileWrite($black_hole, $random_string);
			$this->fileClose($black_hole);
		}
	}
}
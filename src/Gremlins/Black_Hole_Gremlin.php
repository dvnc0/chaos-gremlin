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
		$black_hole = fopen('/dev/null', 'w');
		fwrite($black_hole, 'Black Hole Gremlin');
		$size = rand(1000000, 9999999999);
		$random_string = str_repeat('1', $size);
		fwrite($black_hole, $random_string);
		fclose($black_hole);
	}
}
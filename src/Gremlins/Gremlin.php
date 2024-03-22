<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

abstract class Gremlin {
	public array $settings = [];

	/**
	 * Roll a dice to determine if the gremlin should attack
	 *
	 * @return bool
	 */
	protected function rollDice(): bool {
		return (rand(1, 6) <= $this->settings['dice_roll_over_under']);
	}

	/**
	 * Check the probability of the gremlin attacking
	 *
	 * @return bool
	 */
	protected function probabilityCheck(): bool {
		return (rand(1, 100) <= $this->settings['probability']);
	}

	abstract public function attack(): void;
}
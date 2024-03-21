<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

abstract class Gremlin {
	public array $settings = [];

	protected function rollDice(): bool {
		return (rand(1, 6) <= $this->settings['dice_roll_over_under']);
	}

	abstract public function attack(): void;
}
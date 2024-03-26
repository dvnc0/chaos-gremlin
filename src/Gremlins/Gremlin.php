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
	 * Get a random dice roll
	 *
	 * @return int
	 */
	protected function getDiceRoll(): int {
		return rand(1, 6);
	}

	/**
	 * Check the probability of the gremlin attacking
	 *
	 * @return bool
	 */
	protected function probabilityCheck(): bool {
		return (rand(1, 100) <= $this->settings['probability']);
	}

	/**
	 * Write to the log file
	 *
	 * @param string $message message to send to log
	 * @return void
	 */
	protected function writeToLog(string $message): void {
		$log_file    = $this->settings['log_directory'] . '/chaos_gremlin.log';
		$log_message = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
		file_put_contents($log_file, $log_message, FILE_APPEND);
	}

	/**
	 * Get a forked process
	 *
	 * @return int
	 */
	protected function getFork(): int {
		return pcntl_fork();
	}

	/**
	 * Attack the system
	 *
	 * @return void
	 */
	abstract public function attack(): void;
}
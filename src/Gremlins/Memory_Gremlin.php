<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Memory_Gremlin extends Gremlin {

	protected array $memory_store = [];

	/**
	 * Attack the system by consuming memory until the percent set in settings
	 *
	 * @return void
	 */
	public function attack(): void {
		
		$pid = pcntl_fork();
		if ($pid === -1) {
			die('Could not fork');
		} elseif ($pid) {
			$this->writeToLog('Memory Gremlin is attacking the system.');
			return;
		} else {
			$this->writeToLog('Memory Gremlin is using PID: ' . getmypid() . ' to attack the system.');
			$this->consumeMemory();
			exit;
		}
	}

	/**
	 * Consume memory until the percent set in settings
	 *
	 * @return void
	 */
	protected function consumeMemory(): void {
		$max_memory_percent = $this->settings['max_memory_percent'];
		$memory_limit = $this->getMemoryLimit();
		$memory_usage = memory_get_usage(true);
		$used_percent = ($memory_usage / $memory_limit) * 100;

		if ($used_percent < $max_memory_percent) {
			while($used_percent < $max_memory_percent) {
				$this->memory_store[] = $this->getDataChunk();
				$memory_usage = memory_get_usage(true);
				$used_percent = ($memory_usage / $memory_limit) * 100;
			}
		}
	}

	/**
	 * Get a chunk of data, default to 1 mb
	 *
	 * @param integer $size
	 * @return array
	 */
	protected function getDataChunk(int $size = 1): array {
		return [str_repeat('1', 1024 * 1024 * $size)];
	}

	/**
	 * Get the memory limit for PHP or the system
	 *
	 * @return int
	 */
	protected function getMemoryLimit(): int {
		$memory_limit = ini_get('memory_limit');
		if ($memory_limit === '' || strtolower($memory_limit) === 'unlimited' || $memory_limit < 0) {
			$total_memory = shell_exec('free -b | grep "Mem:" | awk \'{print $2}\'');
			$total_memory = (int)trim($total_memory);
			return $total_memory;
		}

		$memory_limit = trim($memory_limit);
		$last = strtolower($memory_limit[strlen($memory_limit) - 1]);
		$memory_limit = (int) $memory_limit;
		
		switch ($last) {
			case 'g':
				$memory_limit *= 1024;
			case 'm':
				$memory_limit *= 1024;
			case 'k':
				$memory_limit *= 1024;
		}

		return $memory_limit;
	}
}
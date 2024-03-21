<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Memory_Gremlin extends Gremlin {

	protected array $memory_store = [];

	/**
	 * Attack the system by consuming memory until the percent set in settings
	 * 
	 * Big ol WIP need to hash this out some more
	 *
	 * @return void
	 */
	public function attack(): void {
		$max_memory_percent = $this->settings['max_memory_percent'];
		$memory_limit = $this->getMemoryLimit();
		$memory_usage = memory_get_usage(true);
		$used_percent = ($memory_usage / $memory_limit) * 100;

		if ($used_percent < $max_memory_percent) {
			while($used_percent < $max_memory_percent) {
				$this->memory_store[] = $this->getMegaByteOfData();
				$memory_usage = memory_get_usage(true);
				$used_percent = ($memory_usage / $memory_limit) * 100;
			}
		}

	}

	protected function getMegaByteOfData(): array {
		$data = [];
		for ($i = 0; $i < 1024; $i++) {
			$data[] = str_repeat('a', 1024);
		}

		return $data;
	}

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
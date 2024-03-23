<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Disk_Gremlin extends Gremlin {

	/**
	 * Attack the system by writing a random amount of data to /dev/null
	 *
	 * @return void
	 */
	public function attack(): void {
		$pid = pcntl_fork();
		if ($pid === -1) {
			die('could not fork');
		} else if ($pid) {
			$this->writeToLog('Disk Gremlin is attacking the system');
			return;
		} else {
			if (!is_dir($this->settings['disk_gremlin_directory'])) {
				mkdir($this->settings['disk_gremlin_directory'], 0777, true);
			}
			$this->writeDataToDisk($this->settings['disk_gremlin_directory'], $this->settings['disk_gremlin_number_files'], $this->settings['disk_gremlin_file_size']);
			exit;
		}
	}

	/**
	 * Generate random data
	 *
	 * @param int $size
	 * @return string
	 */
	protected function generateRandomData(int $size) {
		$data = '';
		for ($i = 0; $i < $size; $i++) {
			$data .= chr(mt_rand(0, 255));
		}
		return $data;
	}

	/**
	 * Write data to disk
	 *
	 * @param string $directory
	 * @param int $num_files
	 * @param int $file_size
	 * @return void
	 */
	function writeDataToDisk(string $directory, int $num_files, int $file_size) {
		for ($i = 0; $i < $num_files; $i++) {
			$data = $this->generateRandomData($file_size);
			$filename = $directory . '/file_' . $i . '.txt';
			file_put_contents($filename, $data);
		}
	}
	
	
}
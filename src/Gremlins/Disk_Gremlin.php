<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

use ChaosGremlin\Traits\File_Helper_Trait;

class Disk_Gremlin extends Gremlin {

	use File_Helper_Trait;
	/**
	 * Attack the system by writing a random amount of data to disk
	 *
	 * @return void
	 */
	public function attack(): void {
		$this->yourDiskBelongsToUs();
		return;
	}

	/**
	 * Write data to disk
	 *
	 * @return void
	 */
	protected function yourDiskBelongsToUs(): void {
		$pid = $this->getFork();
		if ($pid === -1) {
			die('could not fork');
		} else if ($pid) {
			$this->writeToLog('Disk Gremlin is attacking the system');
			return;
		} else {
			if (!is_dir($this->settings['disk_gremlin_directory'])) {
				mkdir($this->settings['disk_gremlin_directory'], 0777, TRUE);
			}
			$this->writeDataToDisk($this->settings['disk_gremlin_directory'], $this->settings['disk_gremlin_number_files'], $this->settings['disk_gremlin_file_size']);
			exit;
		}
	}

	/**
	 * Generate random data
	 *
	 * @param int $size size of data to generate
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
	 * @param string $directory directory to write to
	 * @param int    $num_files number of files to write
	 * @param int    $file_size size of each file
	 * @return void
	 */
	protected function writeDataToDisk(string $directory, int $num_files, int $file_size) {
		$run_hash = md5(uniqid());
		for ($i = 0; $i < $num_files; $i++) {
			$data     = $this->generateRandomData($file_size);
			$filename = $directory . '/file_' . $i . '_' . $run_hash . '.txt';
			$this->filePutContents($filename, $data);
		}
	}
	
	
}
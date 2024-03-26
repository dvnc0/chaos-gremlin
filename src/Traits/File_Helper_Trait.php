<?php
declare(strict_types=1);

namespace ChaosGremlin\Traits;

trait File_Helper_Trait {
	/**
	 * Write to a file
	 * 
	 * Unit test helper
	 *
	 * @param string $file    The file to write to
	 * @param string $message The message to write
	 * @return void
	 * 
	 * @codeCoverageIgnore
	 */
	protected function filePutContents(string $file, string $message): void {
		file_put_contents($file, $message, FILE_APPEND);
	}

	/**
	 * Check if a directory exists
	 *
	 * Unit test helper
	 *
	 * @param string $dir The directory to check
	 * @return bool
	 * 
	 * @codeCoverageIgnore
	 */
	protected function isDir(string $dir): bool {
		return is_dir($dir);
	}

	/**
	 * Check if a directory is writable
	 *
	 * Unit test helper
	 *
	 * @param string $dir The directory to check
	 * @return bool
	 * 
	 * @codeCoverageIgnore
	 */
	protected function isWritable(string $dir): bool {
		return is_writable($dir);
	}

	/**
	 * Check if a function exists
	 *
	 * Unit test helper
	 *
	 * @param string $function The function to check
	 * @return bool
	 * 
	 * @codeCoverageIgnore
	 */
	protected function functionExists(string $function): bool {
		return function_exists($function);
	}

	/**
	 * Open a file
	 *
	 * Unit test helper
	 *
	 * @param string $file The file to open
	 * @param string $mode The mode to open the file in
	 * @return mixed
	 * 
	 * @codeCoverageIgnore
	 */
	protected function fileOpen(string $file, string $mode): mixed {
		return fopen($file, $mode);
	}

	/**
	 * Close a file
	 *
	 * Unit test helper
	 *
	 * @param mixed $handle The file handle to close
	 * @return void
	 * 
	 * @codeCoverageIgnore
	 */
	protected function fileClose(mixed $handle): void {
		fclose($handle);
	}

	/**
	 * Read from a file
	 *
	 * Unit test helper
	 *
	 * @param mixed $handle The file handle to read from
	 * @param int   $length The length to read
	 * @return string
	 * 
	 * @codeCoverageIgnore
	 */
	protected function fileRead(mixed $handle, int $length): string {
		return fread($handle, $length);
	}

	/**
	 * Write to a file
	 *
	 * Unit test helper
	 *
	 * @param mixed  $handle The file handle to write to
	 * @param string $data   The data to write
	 * @return int
	 * 
	 * @codeCoverageIgnore
	 */
	protected function fileWrite(mixed $handle, string $data): int {
		return fwrite($handle, $data);
	}
}

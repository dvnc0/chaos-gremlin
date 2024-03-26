<?php
declare(strict_types=1);

namespace ChaosGremlin;

use ChaosGremlin\Exceptions\ChaosGremlinInstanceException;
use ChaosGremlin\Gremlins\Black_Hole_Gremlin;
use ChaosGremlin\Gremlins\Cpu_Gremlin;
use ChaosGremlin\Gremlins\Die_Gremlin;
use ChaosGremlin\Gremlins\Disk_Gremlin;
use ChaosGremlin\Gremlins\Exception_Gremlin;
use ChaosGremlin\Gremlins\Gremlin;
use ChaosGremlin\Gremlins\Latency_Gremlin;
use ChaosGremlin\Gremlins\Memory_Gremlin;
use ChaosGremlin\Gremlins\Traffic_Gremlin;
use ChaosGremlin\Traits\File_Helper_Trait;

/**
 * The Chaos Gremlin
 */
class Chaos_Gremlin {
	/**
	 * The instance of the Chaos Gremlin
	 *
	 * @var Chaos_Gremlin
	 */
	protected static $instance;
	
	/**
	 * The list of gremlins
	 *
	 * @var array<string,class-string>
	 */
	public array $gremlins = [];
	
	/**
	 * The list of enabled gremlins
	 *
	 * @var array<class-string>
	 */
	public array $enabled_gremlins = [];
	
	/**
	 * The list of custom gremlins
	 *
	 * @var array<string,Gremlin>
	 */
	public array $custom_gremlins = [];
	
	/**
	 * The settings for the Chaos Gremlin
	 *
	 * @var array{
	 * 	probability: int<0,100>,
	 * 	min_latency_seconds: int,
	 * 	max_latency_seconds: int,
	 * 	exception_message: string,
	 * 	dice_roll_over_under: float,
	 * 	max_memory_percent: int<0,100>,
	 * 	disk_gremlin_directory: string,
	 * 	disk_gremlin_number_files: int,
	 * 	disk_gremlin_file_size: int,
	 * 	traffic_requests: int,
	 * 	traffic_url: string,
	 * 	log_directory: string,
	 * 	traffic_gremlin_spawns_gremlins: bool
	 *}
	 */
	protected array $settings = [
		'probability' => 30,
		'min_latency_seconds' => 2,
		'max_latency_seconds' => 10,
		'exception_message' => 'Oh no, an exception gremlin was released!',
		'dice_roll_over_under' => 3.5,
		'max_memory_percent' => 90,
		'disk_gremlin_directory' => '',
		'disk_gremlin_number_files' => 100,
		'disk_gremlin_file_size' => 5 * 1024 * 1024,
		'traffic_requests' => 100,
		'traffic_url' => 'http://localhost:8080',
		'log_directory' => '',
		'traffic_gremlin_spawns_gremlins' => FALSE,
	];

	/**
	 * Use the File Helper Trait
	 */
	use File_Helper_Trait;

	/**
	 * Prevent the instance from being cloned
	 * 
	 * @return void
	 */
	protected function __clone(): void { }

	/**
	 * Prevent from being unserialized
	 * 
	 * @return void
	 */
	public function __wakeup(): void {
		throw new ChaosGremlinInstanceException();
	}

	/**
	 * Prevent from being constructed
	 */
	private function __construct() {
		$this->gremlins = [
			'Latency_Gremlin' => Latency_Gremlin::class,
			'Exception_Gremlin' => Exception_Gremlin::class,
			'Memory_Gremlin' => Memory_Gremlin::class,
			'Die_Gremlin' => Die_Gremlin::class,
			'Black_Hole_Gremlin' => Black_Hole_Gremlin::class,
			'Cpu_Gremlin' => Cpu_Gremlin::class,
			'Disk_Gremlin' => Disk_Gremlin::class,
			'Traffic_Gremlin' => Traffic_Gremlin::class,
		];
	}

	/**
	 * Get the instance of the Chaos Gremlin
	 *
	 * @return Chaos_Gremlin
	 */
	public static function getInstance(): Chaos_Gremlin {
		if (NULL === static::$instance) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Set custom settings
	 *
	 * @param array $custom_settings custom settings
	 * @return void
	 */
	public function settings(array $custom_settings): void {
		$this->settings = array_merge($this->settings, $custom_settings);

		if (empty($this->settings['log_directory'])) {
			throw new ChaosGremlinInstanceException('Log directory not set');
		}
	}

	/**
	 * Enable a built in gremlin
	 *
	 * @param string $gremlin_key key of the gremlin to enable
	 * @return void
	 */
	public function enableGremlin(string $gremlin_key): void {
		if (empty($this->settings['log_directory'])) {
			throw new ChaosGremlinInstanceException('Log directory not set');
		}

		if (!array_key_exists($gremlin_key, $this->gremlins)) {
			throw new ChaosGremlinInstanceException('Gremlin not found!');
		}
		$this->writeToLog('Enabled Gremlin: ' . $gremlin_key);
		$this->enabled_gremlins[] = $this->gremlins[$gremlin_key];
	}

	/**
	 * Enable a custom gremlin that is manually triggered
	 *
	 * @param string  $gremlin_key      key of the gremlin to enable
	 * @param Gremlin $Gremlin_Instance instance of the gremlin
	 * @return void
	 */
	public function addGremlin(string $gremlin_key, Gremlin $Gremlin_Instance): void {
		$Gremlin_Instance->settings          = $this->settings;
		$this->custom_gremlins[$gremlin_key] = $Gremlin_Instance;
		$this->writeToLog('Enabled Custom Gremlin: ' . $gremlin_key);
	}

	/**
	 * Call a custom gremlin
	 *
	 * @param string $gremlin_key key of the gremlin to call
	 * @return void
	 */
	public function summonGremlin(string $gremlin_key): void {
		if (!array_key_exists($gremlin_key, $this->custom_gremlins)) {
			throw new ChaosGremlinInstanceException('Gremlin not found!');
		}

		if (isset($_SERVER['HTTP_CHAOS_GREMLIN_DISABLE'])) {
			$this->writeToLog('Chaos Gremlin is disabled with HTTP_CHAOS_GREMLIN_DISABLE header');
			return;
		}

		$this->custom_gremlins[$gremlin_key]->attack();
		$this->writeToLog('Called Custom Gremlin: ' . $gremlin_key);
	}

	/**
	 * Release a gremlin
	 *
	 * @return void
	 */
	public function release(): void {

		if (isset($_SERVER['HTTP_CHAOS_GREMLIN_DISABLE'])) {
			$this->writeToLog('Chaos Gremlin is disabled with HTTP_CHAOS_GREMLIN_DISABLE header');
			return;
		}

		$this->preReleaseCheckList();

		if ($this->shouldUseGremlin()) {
			$gremlin                    = $this->enabled_gremlins[array_rand($this->enabled_gremlins)];
			$Gremlin_Instance           = new $gremlin();
			$Gremlin_Instance->settings = $this->settings;
			$this->writeToLog('Released Gremlin: ' . $gremlin);
			$Gremlin_Instance->attack();
		}
	}

	/**
	 * Run the pre release check list
	 *
	 * @return void
	 */
	protected function preReleaseCheckList(): void {

		if (empty($this->settings['log_directory'])) {
			throw new ChaosGremlinInstanceException('Log directory not set');
		}

		if (empty($this->enabled_gremlins)) {
			throw new ChaosGremlinInstanceException('No Gremlins enabled');
		}

		if (!$this->isDir($this->settings['log_directory'])) {
			throw new ChaosGremlinInstanceException('Log directory does not exist');
		}

		if (!$this->isWritable($this->settings['log_directory'])) {
			throw new ChaosGremlinInstanceException('Log directory is not writable');
		}

		if (in_array(Disk_Gremlin::class, $this->enabled_gremlins)) {
			if (empty($this->settings['disk_gremlin_directory']) && in_array(Disk_Gremlin::class, $this->enabled_gremlins)) {
				throw new ChaosGremlinInstanceException('Disk Gremlin directory not set');
			}

			if (!$this->isDir($this->settings['disk_gremlin_directory'])) {
				throw new ChaosGremlinInstanceException('Disk Gremlin directory does not exist');
			}

			if (!$this->isWritable($this->settings['disk_gremlin_directory'])) {
				throw new ChaosGremlinInstanceException('Disk Gremlin directory is not writable');
			}
		}

		if (!$this->functionExists('pcntl_fork')) {
			throw new ChaosGremlinInstanceException('pcntl_fork is not installed');
		}
	}

	/**
	 * Check the probability of the gremlin attacking
	 *
	 * @return bool
	 */
	protected function shouldUseGremlin(): bool {
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
		$this->filePutContents($log_file, $log_message);
	}
}
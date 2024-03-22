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
use ChaosGremlin\Gremlins\Service_Gremlin;
use ChaosGremlin\Gremlins\Traffic_Gremlin;

class Chaos_Gremlin {
	private static $instance;
	public array $gremlins = [];
	public array $enabled_gremlins = [];
	public array $custom_gremlins = [];
	protected array $settings = [
		'probability' => 30,
		'min_latency_seconds' => 2,
		'max_latency_seconds' => 10,
		'exception_message' => 'Chaos Gremlin Exception',
		'dice_roll_over_under' => 3.5,
		'max_memory_percent' => 90,
		'disk_gremlin_directory' => './chaos_gremlin',
		'disk_gremlin_number_files' => 100,
		'disk_gremlin_file_size' => 5 * 1024 * 1024,
		'traffic_requests' => 100,
		'traffic_url' => 'http://localhost:8080',
	];

	/**
	 *Prevent the instance from being cloned
	 */
	protected function __clone() { }

	/**
	 * Prevent from being unserialized
	 */
	public function __wakeup() {
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
			'Service_Gremlin' => Service_Gremlin::class,
		];
	}

	/**
	 * Get the instance of the Chaos Gremlin
	 *
	 * @return Chaos_Gremlin
	 */
	public static function getInstance(): Chaos_Gremlin {
		if (null === static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Set custom settings
	 *
	 * @param array $custom_settings
	 * @return void
	 */
	public function settings(array $custom_settings): void {
		$this->settings = array_merge($this->settings, $custom_settings);
	}

	/**
	 * Enable a built in gremlin
	 *
	 * @param string $gremlin_key
	 * @return void
	 */
	public function enableGremlin(string $gremlin_key): void {
		if (!\array_key_exists($gremlin_key, $this->gremlins)) {
			throw new ChaosGremlinInstanceException('Gremlin not found!');
		}
		$this->enabled_gremlins[] = $this->gremlins[$gremlin_key];
	}

	/**
	 * Enable a custom gremlin that is manually triggered
	 *
	 * @param string $gremlin_key
	 * @param Gremlin $Gremlin_Instance
	 * @return void
	 */
	public function enableCustomGremlin(string $gremlin_key, Gremlin $Gremlin_Instance): void {
		$Gremlin_Instance->settings = $this->settings;
		$this->custom_gremlins[$gremlin_key] = $Gremlin_Instance;
	}

	/**
	 * Call a custom gremlin
	 *
	 * @param string $gremlin_key
	 * @return void
	 */
	public function callGremlin(string $gremlin_key): void {
		if (!\array_key_exists($gremlin_key, $this->custom_gremlins)) {
			throw new ChaosGremlinInstanceException('Gremlin not found!');
		}
		$this->custom_gremlins[$gremlin_key]->attack();
	}

	/**
	 * Release a gremlin
	 *
	 * @return void
	 */
	public function release(): void {
		if ($this->shouldUseGremlin()) {
			$gremlin = $this->enabled_gremlins[array_rand($this->enabled_gremlins)];
			$Gremlin_Instance = new $gremlin();
			$Gremlin_Instance->settings = $this->settings;
			$Gremlin_Instance->attack();
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
}
<?php
declare(strict_types=1);

namespace ChaosGremlin;

use ChaosGremlin\Exceptions\ChaosGremlinInstanceException;
use ChaosGremlin\Gremlins\Die_Gremlin;
use ChaosGremlin\Gremlins\Exception_Gremlin;
use ChaosGremlin\Gremlins\Latency_Gremlin;
use ChaosGremlin\Gremlins\Memory_Gremlin;

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
			// 'error' => new Error(),
			// 'bandwidth' => new Bandwidth(),
			// 'packet_loss' => new PacketLoss(),
			// 'blackhole' => new Blackhole(),
			// 'whitelist' => new Whitelist(),
			// 'greylist' => new Greylist(),
			// 'blacklist' => new Blacklist(),
			// 'random' => new Random(),
		];
	}

	public static function getInstance(): Chaos_Gremlin {
		if (null === static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	public function settings(array $custom_settings): void {
		$this->settings = array_merge($this->settings, $custom_settings);
	}

	public function enableGremlin(string $gremlin_key): void {
		if (!\array_key_exists($gremlin_key, $this->gremlins)) {
			throw new ChaosGremlinInstanceException('Gremlin not found!');
		}
		$this->enabled_gremlins[] = $this->gremlins[$gremlin_key];
	}

	public function enableCustomGremlin(string $gremlin): void {
		$this->custom_gremlins[] = $gremlin;
		$this->enableGremlin($gremlin);
	}

	public function release(): void {
		if ($this->shouldUseGremlin()) {
			$gremlin = $this->enabled_gremlins[array_rand($this->enabled_gremlins)];
			$Gremlin_Instance = new $gremlin();
			$Gremlin_Instance->settings = $this->settings;
			$Gremlin_Instance->attack();
		}
	
	}

	protected function shouldUseGremlin(): bool {
		return (rand(1, 100) <= $this->settings['probability']);
	}
}
<?php
declare(strict_types=1);

use ChaosGremlin\Chaos_Gremlin;
use PHPUnit\Framework\TestCase;
use ChaosGremlin\Exceptions\ChaosGremlinInstanceException;
use ChaosGremlin\Gremlins\Gremlin;
use ChaosGremlin\Gremlins\Latency_Gremlin;

/**
 * @covers ChaosGremlin\Chaos_Gremlin
 */
class Chaos_GremlinTest extends TestCase {

	protected Chaos_Gremlin $Chaos_Mock;

	protected function setUp(): void {
		// create a general use mock
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			protected function filePutContents(string $file, string $message): void {
				$this->log_messages[] = $message;
			}

			protected function createGremlinInstance(string $gremlin): Gremlin{
				return new class extends Gremlin {
					public function attack(): void {
						// gremlins attack!
					}
				};
			}
		};

		$this->Chaos_Mock = $Chaos_Mock;
	}

	protected function tearDown(): void {
		$Gremlin = Chaos_Gremlin::getInstance();
		$Gremlin->reset();
	}

	// TESTS

	public function testGetInstancReturnsChaosGremlin() {
		$Gremlin = Chaos_Gremlin::getInstance();
		$this->assertInstanceOf(Chaos_Gremlin::class, $Gremlin);
	}
	
	public function testExceptionTryingToUseConstruct() {
		$this->expectException(Error::class);
		$Gremlin = new Chaos_Gremlin();
	}
	
	public function testEnableGremlinMissingSettingsThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$this->Chaos_Mock->enableGremlin('Black_Hole_Gremlin');
	}

	public function testAddingSettingsWithoutLogDirectoryThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$this->Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
		]);
	}

	public function testSettingsAreApplied() {
		$this->Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$settings = $this->Chaos_Mock->getSettings();

		$this->assertEquals(100, $settings['probability']);
		$this->assertEquals(1, $settings['min_latency_seconds']);
		$this->assertEquals(2, $settings['max_latency_seconds']);
		$this->assertEquals('Test Exception', $settings['exception_message']);
		$this->assertEquals(0.5, $settings['dice_roll_over_under']);
		$this->assertEquals(50, $settings['max_memory_percent']);
		$this->assertEquals('/tmp', $settings['disk_gremlin_directory']);
		$this->assertEquals(100, $settings['traffic_requests']);
	}
	
	public function testEnableUnknownGremlinThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$this->Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);
		$this->Chaos_Mock->enableGremlin('Unknown_Gremlin');
	}

	public function testGremlinsCanBeEnabled() {
		$this->Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$this->Chaos_Mock->enableGremlin('Latency_Gremlin');

		$this->assertTrue(in_array(Latency_Gremlin::class, $this->Chaos_Mock->enabled_gremlins));
	}

	public function testGremlinsCanBeReleased() {
		$this->Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$this->Chaos_Mock->enableGremlin('Latency_Gremlin');
		$this->Chaos_Mock->release();

		$this->assertTrue(in_array(Latency_Gremlin::class, $this->Chaos_Mock->enabled_gremlins));
		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[1], "ChaosGremlin\Gremlins\Latency_Gremlin") > 0);
	}
}
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

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

		unset($_SERVER['HTTP_CHAOS_GREMLIN_DISABLE']);
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

	public function testSummonGremlinThrowsExceptionWithNoCustomGremlin() {
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

		$this->expectException(ChaosGremlinInstanceException::class);
		$this->Chaos_Mock->summonGremlin('No_Gremlin');
	}

	public function testThatCustomGremlinIsSummoned() {
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

		$Custom_Gremlin = new class extends Gremlin {
			public function attack(): void {
				$this->writeToLog('Custom Gremlin Attack!');
			}

			protected function filePutContents(string $file, string $message): void {
				$this->log_messages[] = $message;
			}
		};

		$this->Chaos_Mock->addGremlin('Custom_Gremlin', $Custom_Gremlin);
		$this->Chaos_Mock->summonGremlin('Custom_Gremlin');

		$this->assertTrue(array_key_exists('Custom_Gremlin', $this->Chaos_Mock->custom_gremlins));

		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[0], "Enabled Custom Gremlin: Custom_Gremlin") > 0);
		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[1], "Called Custom Gremlin: Custom_Gremlin") > 0);
	}

	public function testSummonGremlinIsDisabledWithHeader() {
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

		$_SERVER['HTTP_CHAOS_GREMLIN_DISABLE'] = '1';

		$Custom_Gremlin = new class extends Gremlin {
			public function attack(): void {
				throw new Exception('Should not get called in this test');
			}
		};

		$this->Chaos_Mock->addGremlin('Custom_Gremlin', $Custom_Gremlin);
		$this->Chaos_Mock->summonGremlin('Custom_Gremlin', true);

		$this->assertTrue(array_key_exists('Custom_Gremlin', $this->Chaos_Mock->custom_gremlins));

		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[0], "Enabled Custom Gremlin: Custom_Gremlin") > 0);
		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[1], "Chaos Gremlin is disabled with HTTP_CHAOS_GREMLIN_DISABLE header") > 0);
	}

	public function testReleaseGremlinDoesNotAttackWhenDisabled() {
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

		$_SERVER['HTTP_CHAOS_GREMLIN_DISABLE'] = '1';

		$this->Chaos_Mock->enableGremlin('Latency_Gremlin');
		$this->Chaos_Mock->release();

		$this->assertTrue(strpos($this->Chaos_Mock->log_messages[1], "Chaos Gremlin is disabled with HTTP_CHAOS_GREMLIN_DISABLE header") > 0);
	}

	public function testNoEnabledGremlinsThrowsException() {
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

		$this->expectException(ChaosGremlinInstanceException::class);
		$this->Chaos_Mock->release();
	}

	public function testEmptyLogDirectoryThrowsException() {
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

		$this->Chaos_Mock->enableGremlin('Latency_Gremlin');
		$this->Chaos_Mock->adjustSettings('log_directory', '');
		$this->Chaos_Mock->release();
	}

	public function testLogDirectoryIsNotDirThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return false;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Latency_Gremlin');
		$Chaos_Mock->release();
	}

	public function testLogDirectoryIsNotWriteableThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return true;
			}

			protected function isWritable(string $dir): bool {
				return false;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Latency_Gremlin');
		$Chaos_Mock->release();
	}

	public function testDiskGremlinLogDirectoryNotSetThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return true;
			}

			protected function isWritable(string $dir): bool {
				return true;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Disk_Gremlin');
		$Chaos_Mock->adjustSettings('disk_gremlin_directory', '');
		$Chaos_Mock->release();
	}

	public function testDiskGremlinLogNotADirThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return $dir === '/tmp/disk' ? false : true;
			}

			protected function isWritable(string $dir): bool {
				return $dir === '/tmp/disk' ? false : true;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp/disk',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Disk_Gremlin');
		$Chaos_Mock->release();
	}

	public function testDiskGremlinLogDirNotWriteableThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return true;
			}

			protected function isWritable(string $dir): bool {
				return $dir === '/tmp/disk' ? false : true;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp/disk',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Disk_Gremlin');
		$Chaos_Mock->release();
	}

	public function testPcntlDoesNotExistThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return true;
			}

			protected function isWritable(string $dir): bool {
				return true;
			}

			protected function functionExists(string $function): bool {
				return $function === 'pcntl_fork' ? false : true;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp/disk',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Disk_Gremlin');
		$Chaos_Mock->release();
	}

	public function testChaosGremlinWakeUpThrowsException() {
		$this->expectException(ChaosGremlinInstanceException::class);
		$Chaos_Mock = new class extends Chaos_Gremlin {
			public array $log_messages = [];

			public function __construct() { 
				parent::__construct();
			}

			public function adjustSettings($key, $value) {
				$this->settings[$key] = $value;
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

			protected function isDir(string $dir): bool {
				return true;
			}

			protected function isWritable(string $dir): bool {
				return true;
			}

			protected function functionExists(string $function): bool {
				return true;
			}

			protected function getFork(): int {
				return -1;
			}
		};

		$Chaos_Mock->settings([
			'probability' => 100,
			'min_latency_seconds' => 1,
			'max_latency_seconds' => 2,
			'exception_message' => 'Test Exception',
			'dice_roll_over_under' => 0.5,
			'max_memory_percent' => 50,
			'disk_gremlin_directory' => '/tmp/disk',
			'log_directory' => '/tmp',
		]);

		$Chaos_Mock->enableGremlin('Disk_Gremlin');
		$Chaos_Mock->__wakeup();
	}
}
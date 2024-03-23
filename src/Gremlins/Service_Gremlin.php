<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Service_Gremlin extends Gremlin {

	/**
	 * List of services that can be restarted
	 *
	 * @var array
	 */
	protected array $services = [
		'apache2',
		'nginx',
		'mysql',
		'postgresql',
		'php-fpm',
		'mariadb.service',
	];

	/**
	 * Attack the system by restarting a service
	 *
	 * @return void
	 */
	public function attack(): void {
		$pid = pcntl_fork();
		if ($pid === -1) {
			$this->writeToLog('Service Gremlin failed to fork');
			return;
		} elseif ($pid) {
			$this->writeToLog('Service Gremlin is attacking the system.');
			return;
		} else {
			$this->restartService();
			exit;
		}
	}

	protected function restartService(): void {
		$service = $this->services[array_rand($this->services)];
		$this->writeToLog('Service Gremlin is restarting ' . $service);
		
		shell_exec('sudo systemctl restart ' . $service);
		return;
	}
}
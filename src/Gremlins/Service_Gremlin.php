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
		'mariadb',
	];

	/**
	 * Attack the system by restarting a service
	 *
	 * @return void
	 */
	public function attack(): void {
		$service = $this->services[array_rand($this->services)];
		exec("systemctl restart $service");
		$this->writeToLog('Service Gremlin is attacking the system, restarting ' . $service);
		return;
	}
}
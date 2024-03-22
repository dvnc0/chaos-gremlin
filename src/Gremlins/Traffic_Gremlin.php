<?php
declare(strict_types=1);

namespace ChaosGremlin\Gremlins;

class Traffic_Gremlin extends Gremlin {

	/**
	 * Attack the system by adding traffic
	 *
	 * @return void
	 */
	public function attack(): void {
		$this->sendGremlins();
		exit();
	}

	/**
	 * Send gremlins to the target URL
	 *
	 * @return void
	 */
	protected function sendGremlins() {
		for ($i = 0; $i < $this->settings['traffic_requests']; $i++) {
			$pid = pcntl_fork();
			
			if ($pid == -1) {
				// Fork failed
				exit("Error forking process\n");
			} elseif ($pid) {

			} else {
				$this->sendHttpRequest($this->settings['traffic_url']);
				exit();
			}
		}
	}

	/**
	 * Send HTTP request
	 *
	 * @param string $url
	 * @return void
	 */
	protected function sendHttpRequest(string $url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_close($ch);
		return;
	}
}
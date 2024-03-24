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
		return;
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
				$this->writeToLog('Traffic Gremlin is attacking the system');
				continue;
			} else {
				$this->sendHttpRequest($this->settings['traffic_url']);
				$this->writeToLog('Traffic Gremlin is attacking the system, request '. $i . ' sent.');
				exit;
			}
		}

		return;
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

		if ($this->settings['traffic_gremlin_spawns_gremlins'] === false) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['CHAOS_GREMLIN_DISABLE: true']);
		}

		curl_close($ch);
		return;
	}
}
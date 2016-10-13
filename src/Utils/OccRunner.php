<?php
/**
 * @author Victor Dubiniuk <dubiniuk@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace Owncloud\Updater\Utils;

use GuzzleHttp\Client;
use Owncloud\Updater\Console\Application;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Owncloud\Updater\Utils\Locator;

class OccRunner {
	/**
	 * @var Locator $locator
	 */
	protected $locator;

	/**
	 * @var bool
	 */
	protected $canUseProcess;

	/**
	 *
	 * @param Locator $locator
	 * @param bool $canUseProcess
	 */
	public function __construct(Locator $locator, $canUseProcess){
		$this->locator = $locator;
		$this->canUseProcess = $canUseProcess;
	}

	public function setCanUseProcess($canUseProcess){
		$this->canUseProcess = $canUseProcess;
	}

	public function run($command, $args = [], $asJson = false){
		if ($this->canUseProcess){
			$extra = $asJson ? '--output=json' : '';
			$cmdLine = trim($command . ' ' . $extra);
			foreach ($args as $optionTitle => $optionValue){
				if (strpos($optionTitle, '--') === 0){
					$line = trim("$optionTitle=$optionValue");
				} else {
					$line = $optionValue;
				}
				$escapedLine = ProcessUtils::escapeArgument($line);
				$cmdLine .= " $escapedLine";
			}
			return $this->runAsProcess($cmdLine);
		} else {
			if ($asJson){
				$args['--output'] = 'json';
			}
			$response = $this->runAsRequest($command, $args);
			$decodedResponse = json_decode($response, true);
			return $decodedResponse['response'];
		}
	}

	public function runJson($command, $args = []){
		$plain = $this->run($command, $args, true);
		// trim response to always be a valid json. Capture everything between the first and the last curly brace
		preg_match_all('!(\{.*\})!ms', $plain, $matches);
		$clean = isset($matches[1][0]) ? $matches[1][0] : '';
		$decoded = json_decode($clean, true);
		if (!is_array($decoded)){
			throw new \UnexpectedValueException('Could not parse a response for ' . $command . '. Please check if the current shell user can run occ command. Raw output: ' . PHP_EOL . $plain);
		}
		return $decoded;
	}

	protected function runAsRequest($command, $args){
		$application = $this->getApplication();
		$client = new Client();
		$request = $client->createRequest(
			'POST',
			$application->getEndpoint() . $command,
			[
				'timeout' => 0,
				'json' => [
					'token' => $application->getAuthToken(),
					'params'=> $args
				]
			]
		);

		$response = $client->send($request);
		$responseBody = $response->getBody()->getContents();
		return $responseBody;
	}

	protected function getApplication(){
		$container = Application::$container;
		$application = $container['application'];
		return $application;
	}

	protected function runAsProcess($cmdLine){
		$occPath = $this->locator->getPathToOccFile();
		$cmd = "php $occPath --no-warnings $cmdLine";
		$process = new Process($cmd);
		$process->setTimeout(null);
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}
		return $process->getOutput();
	}
}

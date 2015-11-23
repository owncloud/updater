<?php

namespace Owncloud\Updater\Utils;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Owncloud\Updater\Utils\Locator;

class OccRunner {
	/**
	 * @var Locator $locator
	 */
	protected $locator;

	/**
	 *
	 * @param Locator $locator
	 */
	public function __construct(Locator $locator){
		$this->locator = $locator;
	}

	public function run($args){
		$process = new Process($this->locator->getPathToOccFile() . ' ' . $args);
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}
		return $process->getOutput();
	}

	public function runJson($args){
		$plain = $this->run($args . '  --output "json"');
		$decoded = json_decode($plain, true);
		if (!is_array($decoded)){
			throw new \UnexpectedValueException('Could not parse a response for ' . $args . '. Please check if the current shell user can run occ command. Raw output: ' . PHP_EOL . $plain);
		}
		return $decoded;
	}

}

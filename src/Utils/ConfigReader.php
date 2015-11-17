<?php

namespace Owncloud\Updater\Utils;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Owncloud\Updater\Utils\Locator;

class ConfigReader {

	/** @var array Associative array ($key => $value) */
	protected $cache = [];

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

	public function init(){
		$this->load();
	}

	/**
	 * Get a value from OC config by
	 * path key1.key2.key3
	 * @param string $path
	 * @return mixed
	 */
	public function getByPath($path){
		return $this->get(explode('.', $path));
	}

	/**
	 * Get a value from OC config by keys
	 * @param array $keys
	 * @return mixed
	 */
	public function get($keys){
		$config = $this->cache;
		do {
			$key = array_shift($keys);
			if (!count($keys)>0 && !is_array($config)){
				return;
			}
			if (!array_key_exists($key, $config)){
				return;
			}
			$config = $config[$key];
		} while ($keys);
		return $config;
	}

	/**
	 * Get OC Edition
	 * @return string
	 * @throws ProcessFailedException
	 */
	public function getEdition(){
		$process = new Process($this->locator->getPathToOccFile() . ' status --output "json"');
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}
		$rawConfig = $process->getOutput();
		$response = json_decode($rawConfig, true);
		return $response['edition'];
	}

	/**
	 * Export OC config as JSON and parse it into the cache
	 * @throws ProcessFailedException
	 * @throws \UnexpectedValueException
	 */
	private function load(){
		$process = new Process($this->locator->getPathToOccFile() . ' config:list --output "json"');
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}
		$rawConfig = $process->getOutput();
		$this->cache = json_decode($rawConfig, true);
		if (is_null($this->cache)){
			throw new \UnexpectedValueException('Can not parse ownCloud config. Please check if the current shell user can run occ command. Raw output: ' . PHP_EOL . $rawConfig);
		}
	}

}

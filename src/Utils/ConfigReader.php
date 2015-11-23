<?php

namespace Owncloud\Updater\Utils;

use Owncloud\Updater\Utils\OccRunner;

class ConfigReader {

	/** @var array Associative array ($key => $value) */
	protected $cache = [];

	/**
	 * @var OccRunner $occRunner
	 */
	protected $occRunner;

	/**
	 *
	 * @param OccRunner $occRunner
	 */
	public function __construct(OccRunner $occRunner){
		$this->occRunner = $occRunner;
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
		$response = $this->occRunner->runJson('status');
		return $response['edition'];
	}

	/**
	 * Export OC config as JSON and parse it into the cache
	 * @throws ProcessFailedException
	 * @throws \UnexpectedValueException
	 */
	private function load(){
		$this->cache = $this->occRunner->runJson('config:list');
	}

}

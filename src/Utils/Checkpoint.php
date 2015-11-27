<?php

namespace Owncloud\Updater\Utils;

use \Owncloud\Updater\Utils\FilesystemHelper;
use Owncloud\Updater\Utils\Locator;

class Checkpoint {

	/**
	 * @var Locator $locator
	 */
	protected $locator;

	/**
	 * @var Filesystemhelper $fsHelper
	 */
	protected $fsHelper;

	/**
	 *
	 * @param Locator $locator
	 */
	public function __construct(Locator $locator, FilesystemHelper $fsHelper){
		$this->locator = $locator;
		$this->fsHelper = $fsHelper;
	}

	public function create(){
		$checkpointName = $this->getCheckpointName();
		$checkpointPath = $this->locator->getCheckpointDir() . '/' . $checkpointName;
		try{
			$this->fsHelper->mkdir($checkpointPath);
			$core = $this->locator->getRootDirItems();
			foreach ($core as $coreItem){
				$cpItemPath = $checkpointPath . '/' . basename($coreItem);
				$this->fsHelper->copyr($coreItem, $cpItemPath, true);
			}
			//copy config.php
			$configDirSrc = dirname($core[2]) . '/config';
			$configDirDst = $checkpointPath . '/config';
			$this->fsHelper->copyr($configDirSrc, $configDirDst, true);
		} catch (\Exception $ex){
			//var_dump($ex->getMessage());
		}
	}

	public function restore($checkpointId){

	}

	public function show(){
		
	}

	protected function getCheckpointName(){
		$versionString = implode('.', $this->locator->getInstalledVersion());
		return uniqid($versionString . '-');
	}

}

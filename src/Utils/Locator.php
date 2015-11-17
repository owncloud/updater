<?php

namespace Owncloud\Updater\Utils;

class Locator {

	/**
	 * absolute path to ownCloud root
	 * @var string 
	 */
	protected $owncloudRootPath;

	/**
	 * absolute path to updater root
	 * @var string
	 */
	protected $updaterRootPath;

	/**
	 *
	 * @param string $baseDir
	 */
	public function __construct($baseDir){
		$this->updaterRootPath = $baseDir;
		$this->owncloudRootPath = dirname($baseDir);
	}

	public function getDataDir(){
		return $this->updaterRootPath . '/data';
	}

	public function getDownloadBaseDir(){
		return $this->getDataDir() . '/download';
	}

	/**
	 *
	 * @return string
	 */
	public function getPathToOccFile(){
		return $this->owncloudRootPath . '/occ';
	}

	/**
	 *
	 * @return string
	 */
	public function getInstalledVersion(){
		include $this->getPathToVersionFile();

		/** @var $OC_Version string */
		return $OC_Version;
	}

	/**
	 *
	 * @return string
	 */
	public function getBuild(){
		include $this->getPathToVersionFile();

		/** @var $OC_Build string */
		return $OC_Build;
	}

	public function getPathtoConfigFiles($filePostfix = 'config.php'){
		// Only config.php for now
		return [
			$this->owncloudRootPath . '/config/' . $filePostfix
		];
	}

	/**
	 *
	 * @return string
	 */
	public function getPathToVersionFile(){
		return $this->owncloudRootPath . '/version.php';
	}

}

<?php

namespace Owncloud\Updater\Utils;

class Feed {

	/** string $version */
	protected $version;

	/** string $versionString */
	protected $versionString;

	/** string $url */
	protected $url;

	/** string $web */
	protected $web;

	/** array $requiredFeedEntries */
	protected $requiredFeedEntries = [
		'version',
		'versionstring',
		'url'
	];

	/** bool $isValid */
	protected $isValid = true;

	/**
	 *
	 * @param array $data
	 */
	public function __construct($data){
		$missingEntries = [];
		foreach ($this->requiredFeedEntries as $index){
			if (!isset($data[$index]) || empty($data[$index])){
				$missingEntries[] = $index;
				$data[$index] = '';
			}
		}

		if (count($missingEntries)){
			$this->isValid = false;
			//'Got missing or empty fileds for: ' . implode(',', $missingEntries) . '. No updates found.';
		}
		$this->version = $data['version'];
		$this->versionString = $data['versionstring'];
		$this->url = $data['url'];
	}

	/**
	 * Build filename to download as a.b.c.d.zip
	 * @return string
	 */
	public function getDownloadedFileName(){
		$extension = preg_replace('|.*?(\.[^.]*)$|s', '\1', $this->getUrl());
		return $this->getVersion() . $extension;
	}

	/**
	 * Does feed contain all the data required?
	 * @return bool
	 */
	public function isValid(){
		return $this->isValid;
	}

	/**
	 *
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 *
	 * @return string
	 */
	public function getVersionString(){
		return $this->versionString;
	}

	/**
	 * Get url to download a new version from
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 * Get url to download a checksum from
	 * @return string
	 */
	public function getChecksumUrl(){
		return $this->url . '.md5';
	}

}

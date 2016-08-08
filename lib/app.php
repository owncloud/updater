<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2014 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

namespace OCA\Updater;

class App {

	const APP_ID = 'updater';

	/**
	 * @var \OC_L10N
	 */
	public static $l10n;
	
	public static function init() {
		self::$l10n = \OCP\Util::getL10N(self::APP_ID);
		//Allow config page
		\OCP\App::registerAdmin(self::APP_ID, 'admin');
	}
	
	public static function getFeed($helper = false, $config = false){
		if (!$helper){
			$helper = \OC::$server->getHTTPHelper();
		}
		if (!$config){
			$config = \OC::$server->getConfig();
		}
		$updater = new \OC\Updater($helper, $config);
		$feed = $updater->check('https://apps.owncloud.com/updater.php');
		$data = array();
		if (is_array($feed)){
			foreach (array('version', 'versionstring', 'url', 'web') as $field){
				if (isset($feed[$field])){
					$data[$field] = (string) $feed[$field];
				}
			}
		}
		return $data;
	}

	/**
	 * Get app working directory
	 * @return string
	 */
	public static function getBackupBase() {
		return \OCP\Config::getSystemValue("datadirectory", \OC::$SERVERROOT . "/data") . '/updater_backup/';
	}
	
	public static function getTempBase(){
		return \OC::$SERVERROOT . "/_oc-upgrade/";
	}
	
	public static function log($message, $level= \OCP\Util::ERROR) {
		\OCP\Util::writeLog(self::APP_ID, $message, $level);
	}
}

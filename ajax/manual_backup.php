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

\OCP\JSON::checkAdminUser();
\OCP\JSON::callCheck();

set_time_limit(0);
clearstatcache();

try {
	
	$backupPath = Backup::create(-1);

	// zip backup 
	$zip = new \ZipArchive();
	if ($zip->open($backupPath . ".zip", \ZIPARCHIVE::CREATE) === true){
		Helper::addDirectoryToZip($zip, $backupPath);
		$zip->close();
		\OCP\Files::rmdirr($backupPath);
	}

	\OCP\JSON::success(array(
		'backup' => $backupPath
	));
	
} catch (PermissionException $e){
	//Something is not writable|readable
	\OCP\JSON::error(array(
		'message' => $e->getExtendedMessage()
	));
} catch (FsException $e){
	//Backup failed
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => $e->getMessage()
	));
} catch (\Exception $e){
	//Something went wrong. We don't know what
	App::log($e->getMessage());
	\OCP\JSON::error(array(
		'message' => $e->getMessage()
	));
}

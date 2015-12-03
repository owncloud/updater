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

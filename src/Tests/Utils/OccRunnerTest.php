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

namespace Owncloud\Updater\Tests\Utils;



class OccRunnerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testInvalidJson(){
		$occRunner = $this->getMockBuilder('Owncloud\Updater\Utils\OccRunner')
				->setConstructorArgs([
					$this->getLocatorMock(),
					true
				])
				->setMethods(['runAsRequest', 'runAsProcess'])
				->getMock();
		;
		$occRunner->method('runAsRequest')
			->willReturn('not-a-json')
		;
		$occRunner->runJson('status');
	}

	public function testValidJson(){
		$occRunner = $this->getMockBuilder('Owncloud\Updater\Utils\OccRunner')
			->setConstructorArgs([
				$this->getLocatorMock(),
				false
			])
			->setMethods(['runAsRequest', 'runAsProcess'])
			->getMock();
		;
		$occRunner->method('runAsRequest')
			->willReturn('{"exitCode":0,"response":"{\"installed\":true,\"version\":\"9.1.0.10\",\"versionstring\":\"9.1.0 beta 2\",\"edition\":\"\"}\n"}')
		;
		$result = $occRunner->runJson('status');
		$this->assertEquals(
			[
				'installed' => true,
				'version' => "9.1.0.10",
 				'versionstring' => "9.1.0 beta 2",
  				'edition' => ""
			],
			$result
		);

	}

	private function getLocatorMock(){
		$locatorMock = $this->getMockBuilder('Owncloud\Updater\Utils\Locator')
			->disableOriginalConstructor()
			->getMock()
		;
		return $locatorMock;
	}
}

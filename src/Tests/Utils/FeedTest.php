<?php

namespace Owncloud\Updater\Tests\Utils;

use Owncloud\Updater\Utils\Feed;

/**
 * Class FeedTest
 *
 * @package Owncloud\Updater\Tests\Utils
 */
class FeedTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @return array
	 */
	public function resultProvider(){
		return [
			[ [], false ],
			[ [ 'url'=>'123' ], false ],
			[ [ 'url'=>'123', 'version' => '123' ], false ],
			[ [ 'url'=>'123', 'version' => '123', 'versionstring' => '123' ], true ],
		];
	}

	/**
	 * @dataProvider resultProvider
	 */
	public function testValidity($feedData, $expectedValidity){
		$feed = new Feed($feedData);
		$this->assertEquals($expectedValidity, $feed->isValid());
	}

	/**
	 * @return array
	 */
	public function feedFileNameProvider(){
		return [
			[ [ 'url'=>'http://example.org/package.zip', 'version' => '1.2.3', 'versionstring' => '1.2.3' ], '1.2.3.zip' ],
			[ [ 'url'=>'https://download.owncloud.org/community/owncloud-daily-master.tar.bz2', 'version' => '1.2.3', 'versionstring' => '1.2.3' ], '1.2.3.tar.bz2' ],
		];
	}

	/**
	 * @dataProvider feedFileNameProvider
	 */
	public function testGetDowngetDownloadedFileName($feedData, $filename){
		$feed = new Feed($feedData);
		$this->assertEquals($filename, $feed->getDownloadedFileName());
	}
}

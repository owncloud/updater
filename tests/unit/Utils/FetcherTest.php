<?php

namespace Owncloud\Updater\Tests\Utils;

use GuzzleHttp\Client;
use Owncloud\Updater\Utils\ConfigReader;
use Owncloud\Updater\Utils\Fetcher;
use Owncloud\Updater\Utils\Locator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class FetcherTest
 *
 * @package Owncloud\Updater\Tests\Utils
 */
class FetcherTest extends TestCase {
	protected $httpClient;
	protected $locator;
	protected $configReader;

	public function setUp():void {
		$this->httpClient = $this->createMock(Client::class);
		$this->locator = $this->createMock(Locator::class);
		$this->configReader = $this->createMock(ConfigReader::class);

		$map = [
			['apps.core.installedat', '100500'],
			['apps.core.lastupdatedat', '500100'],
			['apps.core.OC_Channel', 'stable'],
			['system.version', '8.2.0.3'],
		];

		$this->configReader
				->method('getByPath')
				->will($this->returnValueMap($map))
		;
		$this->configReader
				->method('getEdition')
				->willReturn('')
		;
		$this->locator
				->method('getBuild')
				->willReturn('2015-03-09T13:29:12+00:00 8db687a1cddd13c2a6fb6b16038d20275bd31e17')
		;
	}

	public function testGetValidFeed() {
		$responseMock = $this->getResponseMock('<?xml version="1.0"?><owncloud>  <version>8.1.3.0</version><versionstring>ownCloud 8.1.3</versionstring>
  <url>https://download.owncloud.org/community/owncloud-8.1.3.zip</url>
  <web>https://doc.owncloud.org/server/8.1/admin_manual/maintenance/upgrade.html</web>
</owncloud>');
		$this->httpClient
				->method('request')
				->willReturn($responseMock)
		;
		$fetcher = new Fetcher($this->httpClient, $this->locator, $this->configReader);
		$feed = $fetcher->getFeed();
		$this->assertInstanceOf('Owncloud\Updater\Utils\Feed', $feed);
		$this->assertTrue($feed->isValid());
		$this->assertSame('8.1.3.0', $feed->getVersion());
	}

	public function testGetEmptyFeed() {
		$responseMock = $this->getResponseMock('');
		$this->httpClient
				->method('request')
				->willReturn($responseMock)
		;
		$fetcher = new Fetcher($this->httpClient, $this->locator, $this->configReader);
		$feed = $fetcher->getFeed();
		$this->assertInstanceOf('Owncloud\Updater\Utils\Feed', $feed);
		$this->assertFalse($feed->isValid());
	}

	public function testGetGarbageFeed() {
		$responseMock = $this->getResponseMock('<!DOCTYPE html><html lang="en"> <head><meta charset="utf-8">');
		$this->httpClient
				->method('request')
				->willReturn($responseMock)
		;
		$fetcher = new Fetcher($this->httpClient, $this->locator, $this->configReader);
		$feed = $fetcher->getFeed();
		$this->assertInstanceOf('Owncloud\Updater\Utils\Feed', $feed);
		$this->assertFalse($feed->isValid());
	}

	/**
	 * @param $body
	 * @return mixed
	 */
	private function getResponseMock($body) {
		$bodyMock = $this->getMockBuilder(StreamInterface::class)
				->disableOriginalConstructor()
				->getMock()
		;
		$bodyMock
				->expects($this->any())
				->method('getContents')
				->willReturn($body)
		;

		$responseMock = $this->getMockBuilder(ResponseInterface::class)
				->disableOriginalConstructor()
				->getMock()
		;
		$responseMock
				->expects($this->any())
				->method('getStatusCode')
				->willReturn(200)
		;
		$responseMock
				->expects($this->any())
				->method('getBody')
				->willReturn($bodyMock)
		;
		return $responseMock;
	}
}

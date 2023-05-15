<?php

namespace Owncloud\Updater\Tests\Utils;

use Owncloud\Updater\Utils\DocLink;

/**
 * Class DocLinkTest
 *
 * @package Owncloud\Updater\Tests\Utils
 */
class DocLinkTest extends \PHPUnit\Framework\TestCase {
	public function testGetServerUrl() {
		$expected = 'https://doc.owncloud.com/server/10.12/admin_manual/installation/installation_wizard.html#strong-perms-label';

		$version = '10.12';
		$relativePart = 'installation/installation_wizard.html#strong-perms-label';

		$docLink = new DocLink($version);
		$this->assertSame($expected, $docLink->getAdminManualUrl($relativePart));
	}

	/**
	 * @return array
	 */
	public function versionDataProvider() {
		return [
			[ '1.2.3.4', 'https://doc.owncloud.com/server/1.2/admin_manual/' ],
			[ '41.421.31.4.7.5.5', 'https://doc.owncloud.com/server/41.421/admin_manual/' ],
			[ '42.24', 'https://doc.owncloud.com/server/42.24/admin_manual/' ],
		];
	}

	/**
	 * @dataProvider versionDataProvider
	 * @param string $version
	 * @param string $expected
	 */
	public function testTrimVersion($version, $expected) {
		$docLink = new DocLink($version);
		$trimmedVersion = $docLink->getAdminManualUrl('');
		$this->assertSame($expected, $trimmedVersion);
	}
}

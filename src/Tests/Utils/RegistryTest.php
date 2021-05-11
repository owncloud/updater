<?php

namespace Owncloud\Updater\Tests\Utils;

use Owncloud\Updater\Utils\Registry;

/**
 * Class RegistryTest
 *
 * @package Owncloud\Updater\Tests\Utils
 */
class RegistryTest extends \PHPUnit\Framework\TestCase {
	public function testGetUnsetValue() {
		$registry = new Registry();
		$value = $registry->get('random_key');
		$this->assertNull($value);
	}

	public function testGetExistingValue() {
		$data = ['someKey' => 'someValue' ];
		$registry = new Registry();
		$registry->set('key', $data);
		$value = $registry->get('key');

		$this->assertSame($data, $value);
	}
}

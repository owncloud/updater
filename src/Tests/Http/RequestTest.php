<?php

namespace Owncloud\Updater\Tests\Http;

use Owncloud\Updater\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {

	public function varsProvider(){
		return [
			[ [], 'abcd', null ],
			[ [ 'post'=> [ 'command' => 'jump'] ], 'dummy',  null ],
			[ [ 'post'=> [ 'command' => 'jump'] ], 'command', 'jump' ],
			[ [ 'post'=> [ 'testArray' => ['key' => 'value'] ] ], 'testArray',  ['key' => 'value'] ],
		];
	}

	/**
	 * @dataProvider varsProvider
	 */
	public function testPostParameter($vars, $key, $expected){
		$request = new Request($vars);
		$actual = $request->postParameter($key);
		$this->assertEquals($expected, $actual);
	}

	public function serverProvider(){
		return [
			[ [], 'abcd', null ],
			[ [ 'headers'=> [ 'command' => 'jump'] ], 'dummy',  null ],
			[ [ 'headers'=> [ 'command' => 'jump'] ], 'command', 'jump' ],
			[ [ 'headers'=> [ 'testArray' => ['key' => 'value'] ] ], 'testArray',  ['key' => 'value'] ],
		];
	}

	/**
	 * @dataProvider serverProvider
	 */
	public function testServerVar($vars, $key, $expected){
		$request = new Request($vars);
		$actual = $request->server($key);
		$this->assertEquals($expected, $actual);
	}

	public function headerProvider(){
		return [
			[ [], 'meow', null ],
			[ [ 'headers'=> [ 'command' => 'jump'] ], 'dummy',  null ],
			[ [ 'headers'=> [ 'command' => 'jump'] ], 'command', null ],
			[ [ 'headers'=> [ 'testArray' => ['key' => 'value'] ] ], 'testArray',  null ],
			[ [ 'headers'=> [ 'HTTP_TESTARRAY' => ['key' => 'value'] ] ], 'testArray', ['key' => 'value'] ],
		];
	}

	/**
	 * @dataProvider headerProvider
	 */
	public function testHeaderVar($vars, $key, $expected){
		$request = new Request($vars);
		$actual = $request->header($key);
		$this->assertEquals($expected, $actual);
	}


	public function hostProvider(){
		return [
			[ [ 'headers'=> [ 'SERVER_NAME' => 'jump' ] ], 'jump', null ],
			[ [ 'headers'=> [ 'HTTP_HOST'=> 'duck', 'SERVER_NAME' => 'jump'] ], 'duck' ],
			[ [ 'headers'=> [ 'HTTP_X_FORWARDED_HOST'=>'go', 'HTTP_HOST'=> 'duck', 'SERVER_NAME' => 'jump'] ], 'go' ],
			[ [ 'headers'=> [ 'HTTP_X_FORWARDED_HOST'=>'go,', 'HTTP_HOST'=> 'duck', 'SERVER_NAME' => 'jump'] ], 'go' ],
			[ [ 'headers'=> [ 'HTTP_X_FORWARDED_HOST'=>'run,forrest,run', 'HTTP_HOST'=> 'duck', 'SERVER_NAME' => 'jump'] ], 'run' ],
		];
	}

	/**
	 * @dataProvider hostProvider
	 * @param $vars
	 * @param $expected
	 */
	public function testGetHost($vars, $expected){
		$request = new Request($vars);
		$actual = $request->getHost();
		$this->assertEquals($expected, $actual);
	}
}

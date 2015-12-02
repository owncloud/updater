<?php

namespace Owncloud\Updater\Command;

class Command extends \Symfony\Component\Console\Command\Command{
	protected $container;

	public function setContainer($container){
		$this->container = $container;
 	}
}

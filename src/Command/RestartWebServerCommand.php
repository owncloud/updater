<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestartWebServerCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:restartWebServer')
				->setDescription('Please Restart Web Server')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
	}

}

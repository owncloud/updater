<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSystemCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:checkSystem')
				->setDescription('System check. System health and if dependencies are OK (we also count the number of files and DB entries and give time estimations based on hardcoded estimation)')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
	}

}

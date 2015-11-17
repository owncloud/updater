<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DisableNotShippedAppsCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:disableNotShippedApps')
				->setDescription('disable 3rdparty/not shipped apps')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$output->writeln($this->getDescription());
	}

}

<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EnableNotShippedAppsCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:enableNotShippedApps')
				->setDescription('try reenable and upgrade 3rdparty/non shipped apps. (one app after another)')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$output->writeln($this->getDescription());
	}

}

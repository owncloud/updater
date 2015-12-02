<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:info')
				->setDescription(
						'Your ownCloud is going to be upgraded'
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$message = sprintf('%s %s',
						$this->getApplication()->getName(),
						$this->getApplication()->getVersion()
		);
		$output->writeln($message);
	}

}

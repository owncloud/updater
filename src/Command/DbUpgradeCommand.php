<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbUpgradeCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:dbUpgrade')
				->setDescription('db schema upgrade')
				->addArgument(
						'simulation', InputArgument::OPTIONAL, ''
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$simulation = strtolower($input->getArgument('simulation'));
		$message = $simulation === 'true' ? ' simulated (optionally, can be done online in advance)' : 'real [danger, might take long]';
		$output->writeln($message);
	}

}

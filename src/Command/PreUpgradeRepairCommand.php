<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PreUpgradeRepairCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:preUpgradeRepair')
				->setDescription('Repair and cleanup (pre upgrade, DB collations update, ..) [danger, might take long]')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
	}

}

<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostUpgradeRepairCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:postUpgradeRepair')
				->setDescription('repair and cleanup step 1 (post upgrade, repair legacy storage, ..) [danger, might take long]')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
	}

}

<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckpointCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:checkpoint')
				->setDescription('Create or restore owncloud files')
				->addOption(
						'create', null, InputOption::VALUE_OPTIONAL, 'create a checkpoint'
				)
				->addOption(
						'restore', null, InputOption::VALUE_REQUIRED, 'revert files to a given checkpoint'
				)
				->addOption(
						'list', null, InputOption::VALUE_OPTIONAL, 'show all checkpoints'
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$container = $this->getApplication()->getContainer();
		$checkpoint = $container['utils.checkpoint'];
		if ($input->getOption('create')){
			$checkpoint->create();
		} elseif ($input->getOption('restore')) {
			$checkpoint->restore($input->getOption('restore'));
		} else {
			$checkpoint->show();
		}
	}

}

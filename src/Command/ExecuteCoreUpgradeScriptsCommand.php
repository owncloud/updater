<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Owncloud\Updater\Utils\OccRunner;

class ExecuteCoreUpgradeScriptsCommand extends Command {

	/**
	 * @var OccRunner $occRunner
	 */
	protected $occRunner;

	public function __construct($occRunner){
		parent::__construct();
		$this->occRunner = $occRunner;
	}

	protected function configure(){
		$this
				->setName('upgrade:executeCoreUpgradeScripts')
				->setDescription('execute core upgrade scripts
[danger, might take long]')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$locator = $this->container['utils.locator'];
		$fsHelper = $this->container['utils.filesystemhelper'];
		
		$plain = $this->occRunner->run('upgrade');
		$output->writeln($plain);
	}

}

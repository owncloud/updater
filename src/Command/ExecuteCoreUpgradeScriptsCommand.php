<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Owncloud\Updater\Utils\Locator;

class ExecuteCoreUpgradeScriptsCommand extends Command {

	/**
	 * @var Locator $locator
	 */
	protected $locator;

	public function __construct($locator){
		parent::__construct();
		$this->locator = $locator;
	}

	protected function configure(){
		$this
				->setName('upgrade:executeCoreUpgradeScripts')
				->setDescription('execute core upgrade scripts
[danger, might take long]')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$process = new Process($this->locator->getPathToOccFile() . ' upgrade');
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}

		$output->writeln($process->getOutput());
	}

}

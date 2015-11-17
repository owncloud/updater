<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Owncloud\Updater\Utils\Locator;

class MaintenanceModeCommand extends Command {

	/**
	 * @var Locator $locator
	 */
	protected $locator;

	/**
	 * Constructor
	 *
	 * @param Locator $locator
	 */
	public function __construct(Locator $locator){
		parent::__construct();
		$this->locator = $locator;
	}

	protected function configure(){
		$this
				->setName('upgrade:maintenanceMode')
				->setDescription('Toggle maintenance mode')
				->addOption(
						'on', null, InputOption::VALUE_NONE, 'enable maintenance mode'
				)
				->addOption(
						'off', null, InputOption::VALUE_NONE, 'disable maintenance mode'
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$mode = '';
		if ($input->getOption('on')){
			$mode = '--on';
		} elseif ($input->getOption('off')){
			$mode = '--off';
		}
		$process = new Process(
				$this->locator->getPathToOccFile() . ' maintenance:mode ' . ProcessUtils::escapeArgument($mode)
		);
		$process->run();

		if (!$process->isSuccessful()){
			throw new ProcessFailedException($process);
		}

		$output->writeln($process->getOutput());
	}

}

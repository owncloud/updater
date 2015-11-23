<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessUtils;
use Owncloud\Updater\Utils\OccRunner;

class MaintenanceModeCommand extends Command {

	/**
	 * @var OccRunner $occRunner
	 */
	protected $occRunner;

	/**
	 * Constructor
	 *
	 * @param OccRunner $occRunner
	 */
	public function __construct(OccRunner $occRunner){
		parent::__construct();
		$this->occRunner = $occRunner;
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

		if ($mode !== ''){
			$mode = ProcessUtils::escapeArgument($mode);
		}

		$response =  $this->occRunner->run('maintenance:mode ' . $mode);
		$output->writeln($response);
	}

}

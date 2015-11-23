<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class StartCommand extends Command {

	protected $stack = [
		[ 'command' => 'upgrade:info'],
		[ 'command' => 'upgrade:checkSystem'],
		[ 'command' => 'upgrade:maintenanceMode', '--on' => '1'],
		[ 'command' => 'upgrade:detect'],
		[ 'command' => 'upgrade:backupDb'],
		[ 'command' => 'upgrade:backupData'],
		[ 'command' => 'upgrade:preUpgradeRepair'],
		[ 'command' => 'upgrade:dbUpgrade', 'simulation' => 'true'],
		[ 'command' => 'upgrade:dbUpgrade'],
		[ 'command' => 'upgrade:disableNotShippedApps'],
		[ 'command' => 'upgrade:executeCoreUpgradeScript'],
		[ 'command' => 'upgrade:upgradeShippedApps'],
		[ 'command' => 'upgrade:enableNotShippedApps'],
		[ 'command' => 'upgrade:cleanCache'],
		[ 'command' => 'upgrade:postUpgradeRepair'],
		[ 'command' => 'upgrade:restartWebServer'],
		[ 'command' => 'upgrade:updateConfig'],
		[ 'command' => 'upgrade:maintenanceMode', '--off' => '1'],
		[ 'command' => 'upgrade:postUpgradeCleanup'],
	];

	protected function configure(){
		$this
				->setName('upgrade:start')
				->setDescription('go through the flow')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$app = $this->getApplication();
		foreach ($this->stack as $command){
			$input = new ArrayInput($command);
			$returnCode = $app->doRun($input, $output);
			if ($returnCode != 0){
				// Something went wrong
				break;
			}
		}
		$output->writeln('Done');
	}

}

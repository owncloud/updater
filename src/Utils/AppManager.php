<?php

namespace Owncloud\Updater\Utils;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessUtils;
use Owncloud\Updater\Utils\OccRunner;

class AppManager {

	/**
	 * @var OccRunner $occRunner
	 */
	protected $occRunner;

	/**
	 * @var array $disabledApps
	 */
	protected $disabledApps = [];

	/**
	 *
	 * @param OccRunner $occRunner
	 */
	public function __construct(OccRunner $occRunner){
		$this->occRunner = $occRunner;
	}

	public function disableApp($appId){
		try{
			$this->occRunner->run('app:disable ' . ProcessUtils::escapeArgument($appId));
		} catch (\Exception $e){
			return false;
		}
		return true;
	}

	public function enableApp($appId){
		try{
			$this->occRunner->run('app:enable ' . ProcessUtils::escapeArgument($appId));
			array_unshift($this->disabledApps, $appId);
		} catch (\Exception $e){
			return false;
		}
		return true;
	}

	public function disableNotShippedApps(OutputInterface $output = null){
		$notShippedApps = $this->occRunner->runJson('app:list --shipped false');
		$appsToDisable = array_keys($notShippedApps['enabled']);
		foreach ($appsToDisable as $appId){
			$result = $this->disableApp($appId);
			$status = $result ? '<info>success</info>' : '<error>failed</error>';
			if (!is_null($output)){
				$message = sprintf('Disable app %s: [%s]', $appId, $status);
				$output->writeln($message);
			}
		}
	}

	public function reenableNotShippedApps(OutputInterface $output = null){
		foreach ($this->disabledApps as $appId){
			$result = $this->enableApp($appId);
			$status = $result ? '<info>success</info>' : '<error>failed</error>';
			if (!is_null($output)){
				$message = sprintf('Enable app %s: [%s]', $appId, $status);
				$output->writeln($message);
			}
		}
	}

}

<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Event\ProgressEvent;
use Owncloud\Updater\Utils\Fetcher;
use Owncloud\Updater\Utils\Feed;
use Owncloud\Updater\Utils\ConfigReader;
use Owncloud\Updater\Utils\ZipExtractor;

class DetectCommand extends Command {

	/**
	 * @var Fetcher $fetcher
	 */
	protected $fetcher;

	/**
	 * @var ConfigReader $configReader
	 */
	protected $configReader;

	/**
	 * Constructor
	 *
	 * @param Fetcher $fetcher
	 * @param ConfigReader $configReader
	 */
	public function __construct(Fetcher $fetcher, ConfigReader $configReader){
		parent::__construct();
		$this->fetcher = $fetcher;
		$this->configReader = $configReader;
	}

	protected function configure(){
		$this
				->setName('upgrade:detect')
				->setDescription('Detect
- 1. currently existing code, 
- 2. version in config.php, 
- 3. online available verison.
(ASK) what to do? (download, upgrade, abort, …)')
				->addOption(
						'exit-if-none', null, InputOption::VALUE_NONE, 'exit with non-zero status code if new version is not found'
				)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$container = $this->getApplication()->getContainer();
		$locator = $container['utils.locator'];
		$fsHelper = $container['utils.filesystemhelper'];
		try{
			$currentVersion = $this->configReader->getByPath('system.version');
			if (!strlen($currentVersion)){
				throw new \UnexpectedValueException('Could not detect installed version.');
			}

			$this->getApplication()->getLogger()->info('ownCloud ' . $currentVersion . 'found');
			$output->writeln('Current version is ' . $currentVersion);

			$feed = $this->fetcher->getFeed();
			if ($feed->isValid()){
				$output->writeln($feed->getVersionString() . ' is found online');
				$path = $this->fetcher->getBaseDownloadPath($feed);
				$fileExists = $this->isCached($feed, $output);
				if (!$fileExists){
					$this->fetcher->getOwncloud($feed, function (ProgressEvent $e) use ($output) {
					$percentString = '';
					if ($e->downloadSize){
						$percent = intval(100* $e->downloaded / $e->downloadSize );
						$percentString = $percent . '%';
					}
    $output->write( 'Downloaded ' . $percentString . ' (' . $e->downloaded . ' of ' . $e->downloadSize . ")\r");
});
					if (md5_file($path) !== $this->fetcher->getMd5($feed)){
						$output->writeln('Downloaded ' . $feed->getDownloadedFileName() . '. Checksum is incorrect.');
						@unlink($path);
					} else {
						$fileExists = true;
					}
				}
				if ($fileExists){
					$fullExtractionPath = $locator->getExtractionBaseDir() . '/' . $feed->getVersion();
					if (!file_exists($fullExtractionPath)){
						try {
							$fsHelper->mkdir($fullExtractionPath, true);
						} catch (\Exception $ex) {
							$output->writeln('Unable create directory ' . $fullExtractionPath);
						}
					}
					$output->writeln('Extracting source into ' . $fullExtractionPath);

					$zipExtractor = new ZipExtractor($path, $fullExtractionPath);
					try {
						$zipExtractor->extract();
					} catch (\Exception $ex) {
						$output->writeln('Extraction has been failed');
						$fsHelper->removeIfExists($locator->getExtractionBaseDir());
					}
				}
			} else {
				$output->writeln('No updates found online.');
				if ($input->getOption('exit-if-none')){
					exit(4);
				}
			}
		} catch (\Exception $e){
			$this->getApplication()->getLogger()->error($e->getMessage());
			exit(2);
		}
	}

	public function isCached(Feed $feed, OutputInterface $output){
		$path = $this->fetcher->getBaseDownloadPath($feed);
		$fileExists = file_exists($path);
		if ($fileExists){
			if (md5_file($path) === $this->fetcher->getMd5($feed)){
				$output->writeln('Already downloaded ' . $feed->getVersion() . ' with a correct checksum found. Reusing.');
			} else {
				$output->writeln('Already downloaded ' . $feed->getVersion() . ' with an invalid checksum found. Removing.');
				@unlink($path);
				$fileExists = false;
			}
		}
		return $fileExists;
	}

}

<?php

namespace Owncloud\Updater\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Owncloud\Updater\Utils\Collection;

class CheckSystemCommand extends Command {

	protected function configure(){
		$this
				->setName('upgrade:checkSystem')
				->setDescription('System check. System health and if dependencies are OK (we also count the number of files and DB entries and give time estimations based on hardcoded estimation)')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		$container = $this->getApplication()->getContainer();
		$locator = $container['utils.locator'];
		$fsHelper = $container['utils.filesystemhelper'];
		$collection = new Collection();

		$rootDirItems= $locator->getRootDirItems();
		foreach ($rootDirItems as $item){
			$fsHelper->checkr($item, $collection);
		}
		$notReadableFiles = $collection->getNotReadable();
		$notWritableFiles = $collection->getNotWritable();

		if (count($notReadableFiles)){
			$output->writeln('<error>The following files and directories are not readable:</error>');
			$output->writeln($this->longArrayToString($notReadableFiles));
		}

		if (count($notWritableFiles)){
			$output->writeln('<error>The following files and directories are not writable:</error>');
			$output->writeln($this->longArrayToString($notWritableFiles));
		}

		if (count($notReadableFiles) || count($notWritableFiles)){
			$output->writeln('<info>Please check if owner and permissions fot these files are correct.</info>');
			$output->writeln('See https://doc.owncloud.org/server/9.0/admin_manual/installation/installation_wizard.html#strong-perms-label for details.</info>');
		}
	}

	protected function longArrayToString($array){
		if (count($array)>7){
			$shortArray = array_slice($array, 0, 7);
			$more = sprintf('... and %d more items', count($array) - count($shortArray));
			array_push($shortArray, $more);
		} else {
			$shortArray = $array;
		}
		$string = implode(PHP_EOL, $shortArray);
		return $string;
	}

}

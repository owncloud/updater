<?php

namespace Owncloud\Updater\Utils;

class FilesystemHelper {

	/**
	 * Wrapper for mkdir
	 * @param string $path
	 * @param bool $isRecursive
	 * @throws \Exception on error
	 */
	public function mkdir($path, $isRecursive = false){
		if (!mkdir($path, 0755, $isRecursive)){
			throw new \Exception("Unable to create $path");
		}
	}

	/**
	 * Copy recursive
	 * @param string $src  - source path
	 * @param string $dest - destination path
	 * @throws \Exception on error
	 */
	public function copyr($src, $dest, $stopOnError = true){
		if (is_dir($src)){
			if (!is_dir($dest)){
				try{
					$this->mkdir($dest);
				} catch (\Exception $e){
					if ($stopOnError){
						throw $e;
					}
				}
			}
			$files = scandir($src);
			foreach ($files as $file){
				if (!in_array($file, [".", ".."])){
					$this->copyr("$src/$file", "$dest/$file", $stopOnError);
				}
			}
		} elseif (file_exists($src)){
			if (!copy($src, $dest) && $stopOnError){
				throw new \Exception("Unable to copy $src to $dest");
			}
		}
	}

	/**
	 * Moves file/directory
	 * @param string $src  - source path
	 * @param string $dest - destination path
	 * @throws \Exception on error
	 */
	public function move($src, $dest){
		if (!rename($src, $dest)){
			throw new \Exception("Unable to move $src to $dest");
		}
	}

	/**
	 * Check permissions recursive
	 * @param string $src  - path to check
	 * @param Collection $collection - object to store incorrect permissions
	 */
	public function checkr($src, $collection){
		if (!file_exists($src)){
			return;
		}
		if (!is_writable($src)){
			$collection->addNotWritable($src);
		}
		if (!is_readable($src)){
			$collection->addNotReadable($src);
		}
		if (is_dir($src)){
			$files = scandir($src);
			foreach ($files as $file){
				if (!in_array($file, [".", ".."])){
					$this->checkr("$src/$file", $collection);
				}
			}
		}
	}

	public function removeIfExists($path) {
		if (!file_exists($path)) {
			return;
		}

		if (is_dir($path)) {
			$this->rmdirr($path);
		} else {
			@unlink($path);
		}
	}

	protected function rmdirr($dir) {
		if(is_dir($dir)) {
			$files = scandir($dir);
			foreach($files as $file) {
				if ($file != "." && $file != "..") {
					$this->rmdirr("$dir/$file");
				}
			}
			@rmdir($dir);
		}elseif(file_exists($dir)) {
			@unlink($dir);
		}
		if(file_exists($dir)) {
			return false;
		}else{
			return true;
		}
	}

}

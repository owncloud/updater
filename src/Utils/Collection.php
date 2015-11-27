<?php

namespace Owncloud\Updater\Utils;

class Collection {
	private $notReadable = [];
	private $notWritable = [];

	public function reset(){
		$this->notReadable = [];
		$this->notWritable = [];
	}

	public function addNotReadable($item) {
		if (!in_array($item, $this->notReadable)) {
			$this->notReadable[] = $item;
		}
	}

	public function addNotWritable($item) {
		if (!in_array($item, $this->notWritable)) {
			$this->notWritable[] = $item;
		}
	}

	public function getNotReadable(){
		return $this->notReadable;
	}

	public function getNotWritable(){
		return $this->notWritable;
	}
}

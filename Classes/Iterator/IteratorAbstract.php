<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate\Iterator;

abstract class IteratorAbstract extends \FilterIterator
{

	protected $folderPath;

	protected $dirname;

	public function __construct($pages, $folder)
	{
		$this->folderPath = $folder;
		$this->dirname = dirname($this->folderPath);
		parent::__construct($pages);
	}

	public function accept()
	{
		$page = $this->current();
		$path = $page->getFilePath();
		if ($this->folderPath === $path) {
			return false;
		}

		return $this->check($page);
	}

	/**
	 * @param $page
	 * @return bool
	 */
	protected abstract function check($page);

}

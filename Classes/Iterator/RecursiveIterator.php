<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate\Iterator;

class RecursiveIterator extends IteratorAbstract
{

	public function check($page)
	{
		$path = $page->getFilePath();
		$dirname = dirname($path);
		if ($dirname === $this->dirname) {
			return true;
		} elseif (strpos($dirname, $this->dirname) === 0) {
			return true;
		}

		return false;
	}

}

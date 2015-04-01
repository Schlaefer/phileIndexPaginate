<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate\Iterator;

class RecursiveIterator extends IteratorAbstract
{

	public function check($page)
	{
		$path = $page->getFilePath();

		if (basename($path) === 'index' . CONTENT_EXT) {
			return false;
		}

		$folder = dirname($path);
		if ($folder === $this->dirname) {
			return true;
		} elseif (strpos($folder, $this->dirname) === 0) {
			return true;
		}

		return false;
	}

}

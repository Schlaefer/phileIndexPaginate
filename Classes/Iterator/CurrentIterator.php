<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate\Iterator;

class CurrentIterator extends IteratorAbstract
{

	public function check($page)
	{
		$path = $page->getFilePath();

		return dirname($path) === $this->dirname;
	}

}

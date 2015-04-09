<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

use Phile\Exception\PluginException;
use Phile\Plugin\Siezi\PhileIndexPaginate\Iterator\CurrentIterator;
use Phile\Plugin\Siezi\PhileIndexPaginate\Iterator\RecursiveIterator;
use Phile\Repository\Page;

class Plugin extends \Phile\Plugin\Siezi\PhileMagicPlugin\Plugin
{

    protected $types = ['current', 'recursive'];

	protected $events = [
        'config_loaded' => 'onConfig',
        'after_resolve_page' => 'onGetPage',
        'template_engine_registered' => 'onSetTemplateVars'
	];

    protected $paginator;

    protected function onConfig() {
        if (empty($this->settings['templateBasePath'])) {
            $this->settings['templateBasePath'] = $this->getPluginPath();
        }
    }

    protected function onGetPage($data) {
        $page = $data['page'];
        $this->settings['uri'] = $page->getUrl();

		// @todo 1.5 get raw content end remove <p> in regex
		$content = $page->getContent();
		$regex = '/(<p>)?\(folder-index:\s+(?P<type>\S*?)\)(<\/p>)?/';

		if (!preg_match($regex, $content, $matches)) {
			return;
		}

		if (empty($matches['type']) || !in_array($matches['type'], $this->types)) {
			throw new PluginException("folder-index type not recognized");
		}
		$type = $matches['type'];

        $pages = $this->getAllPages($page, $type);
		$paginator = Paginator::build($pages, $this->settings['itemsPerPage']);
        $out = (new Renderer($this->settings))->render($paginator);

		if (empty($out)) {
            throw new PluginException('folder-index rendering failed');
		}

		$out = str_replace('\\', '\\\\', $out);
		$out = preg_replace($regex, $out, $content);
        $data['page']->setContent($out);

        $this->paginator = $paginator;
	}

    protected function getAllPages($rootPage, $type) {
        $repository = new Page();
        $pages = $repository->findAll(['pages_order' => $this->settings['order']]);
        $pages = new \ArrayIterator($pages);
        if ($type === 'recursive') {
            $iterator = new RecursiveIterator($pages, $rootPage->getFilePath());
        } else {
            $iterator = new CurrentIterator($pages, $rootPage->getFilePath());
        }
        $results = [];
        foreach ($iterator as $subPage) {
            $results[] = $subPage;
        }
        return $results;
    }

    protected function onSetTemplateVars($data) {
        if (empty($this->paginator)) {
            return;
        }
        $data['data']['paginator'] = $this->paginator;
    }

}

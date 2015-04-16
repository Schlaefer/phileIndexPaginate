<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

use Phile\Exception\PluginException;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Siezi\PhileIndexPaginate\Iterator\CurrentIterator;
use Phile\Plugin\Siezi\PhileIndexPaginate\Iterator\RecursiveIterator;
use Phile\Repository\Page;

class Plugin extends AbstractPlugin
{

    protected $types = ['current', 'recursive'];

	protected $events = [
        'config_loaded' => 'onConfig',
        'after_resolve_page' => 'onGetPage',
        'template_engine_registered' => 'onSetTemplateVars',
        'after_render_template' => 'onAfterRenderTemplate'
	];

    protected $settings = [
        'regex' => '/(<p>)?\(folder-index:\s+(?P<type>\S*?)\)(<\/p>)?/'
    ];

    protected $paginator;

    protected $html;

    protected function onConfig() {
        if (empty($this->settings['templateBasePath'])) {
            $this->settings['templateBasePath'] = $this->getPluginPath();
        }
    }

    protected function onGetPage($data) {
        $page = $data['page'];
        $this->settings['uri'] = $page->getUrl();

		$content = $page->getContent();

		if (!preg_match($this->settings['regex'], $content, $matches)) {
			return;
		}

		if (empty($matches['type']) || !in_array($matches['type'], $this->types)) {
			throw new PluginException("folder-index type not recognized");
		}
		$type = $matches['type'];

        $pages = $this->getAllPages($page, $type);
		$paginator = Paginator::build($pages, $this->settings['itemsPerPage']);
        $html = (new Renderer($this->settings))->render($paginator);

		if (empty($html)) {
            throw new PluginException('folder-index rendering failed');
		}

        $this->html = $html;
        $this->paginator = $paginator;
	}

    protected function onAfterRenderTemplate($data) {
        if (empty($this->html)) {
            return;
        }

        $html = $this->html;
        $html = str_replace('\\', '\\\\', $html);
        $html = preg_replace($this->settings['regex'], $html, $data['output']);
        $data['output'] = $html;

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

<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Utility;
use Phile\Plugin\AbstractPlugin;
use Phile\Gateway\EventObserverInterface;
use Phile\Repository\Page;

class Plugin extends AbstractPlugin implements EventObserverInterface {

    protected $registeredEvents = [
      'request_uri' => 'onRequestUri',
      'before_parse_content' => 'onBeforeParseContent',
    ];

    protected $active = true;

    protected $page;

    public function __construct() {
        foreach ($this->registeredEvents as $event => $method) {
            Event::registerEvent($event, $this);
        }
    }

    public function on($eventKey, $data = null) {
        $method = $this->registeredEvents[$eventKey];
        $this->{$method}($data);
    }

    protected function onRequestUri($data) {
        $this->settings['uri'] = $data['uri'];
    }

    protected function onBeforeParseContent($data) {
        if (!$this->active) {
            return;
        }
		// avoid recursion on index file @todo 1.5
		$this->active = false;

        $repository = new Page();
		$page = $repository->findByPath($this->settings['uri']);
		// @todo 1.5 get raw content end remove <p> in regex
		$content = $page->getContent();
		$regex = '/(<p>)?\(folder-index: current\)(<\/p>)?/';
		if (!preg_match($regex, $content)) {
			return;
		}

		if (empty($this->settings['templateBasePath'])) {
			$this->settings['templateBasePath'] = $this->getPluginPath();
		}

		$current = $page->getFilePath();
		$folder = dirname($current);
		$pages = $repository->findAll(['pages_order' => $this->settings['order']]);
		$pages = array_filter($pages, function($page) use ($current, $folder) {
			$path = $page->getFilePath();
			if ($current === $path) {
				return false;
			}
			return dirname($path) === $folder;
		});

		$paginator = Paginator::build($pages, $this->settings['itemsPerPage']);
		$out = (new Renderer($this->settings))->render($paginator);

		if (!$out) {
			// @todo 1.5
			Utility::redirect(Utility::getBaseUrl() . '/' . $this->page->getUrl(), 301);
		}

		$vars = Registry::get('templateVars');
		$vars['paginator'] = $paginator;
		Registry::set('templateVars', $vars);

		$out = preg_replace($regex, $out, $content);

		$data['page']->setContent($out);
    }

    // @todo 1.5
    protected function getPluginPath($subPath = '') {
        return PLUGINS_DIR . 'siezi/phileIndexPaginate/' . $subPath;
    }

}

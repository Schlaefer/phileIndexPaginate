<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

use Phile\Core\Event;
use Phile\Core\Utility;
use Phile\Plugin\AbstractPlugin;
use Phile\Gateway\EventObserverInterface;
use Phile\Repository\Page;

class Plugin extends AbstractPlugin implements EventObserverInterface {

    protected $registeredEvents = [
      'request_uri' => 'onRequestUri',
      'before_parse_content' => 'render',
    ];

    protected $active = false;

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
        $page = $this->indexPage($data['uri']);
        if (!$page) {
            return;
        }

        $this->active = true;
        $this->page = $page;
        $this->settings['uri'] = $data['uri'];

        if (empty($this->settings['templateBasePath'])) {
            $this->settings['templateBasePath'] = $this->getPluginPath();
        }
    }

    protected function indexPage($pageId) {
        // @todo 1.5
        $repository = new Page();
        $page = $repository->findByPath($pageId);
        if (!$page) {
            return false;
        }
        $content = str_replace(["\n", "\r"], '', $page->getContent());
        if (!empty($content)) {
            return false;
        }
        if ($this->isIndexPage($page)) {
            return $page;
        }
        return false;
    }

    // @todo 1.5
    protected function isIndexPage($page) {
        return (bool)preg_match('/\/index' . CONTENT_EXT . '$/', $page->getFilePath());
    }

    protected function render($data) {
        if (!$this->active) {
            return;
        }
        // avoid recursion on index file @todo 1.5
        $this->active = false;

        $repository = new Page();
        $pages = $repository->findAll(['pages_order' => $this->settings['order']]);

        $folder = dirname($this->page->getFilePath());
        $pages = array_filter($pages, function($page) use ($folder) {
            // ignore index file @todo 1.5
            if ($this->isIndexPage($page)) {
                return false;
            }
            return dirname($page->getFilePath()) === $folder;
        });

        $paginator = new PagePaginator($this->settings);
        $out = $paginator->render($pages);

        if (!$out) {
            // @todo 1.5
            Utility::redirect(Utility::getBaseUrl() . '/' . $this->page->getUrl(), 301);
        }

        $data['page']->setContent($out);
    }

    // @todo 1.5
    protected function getPluginPath($subPath = '') {
        return PLUGINS_DIR . 'siezi/phileIndexPaginate/' . $subPath;
    }

}

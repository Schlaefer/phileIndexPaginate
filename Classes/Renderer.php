<?php

namespace Phile\Plugin\Siezi\PhileIndexPaginate;

use Phile\Core\Router;

class Renderer
{

	protected $settings = [];

	protected $renderer;

	public function __construct($settings)
	{
		$this->settings = $settings;

		return $this;
	}

	public function render(array $paginator)
	{
		if (!$paginator) {
			return false;
		}
		$out = [];
		foreach ($paginator['items'] as $page) {
			$out[] = $this->renderTemplate(
				'content',
				[
					'base_url' => (new Router())->getBaseUrl(),
					'current_page' => $page,
					'meta' => $page->getMeta(),
					'content' => $page->getContent(),
				]
			);
		}

		$divider = $this->settings['templates']['divider'];
		$out = implode($divider, $out);

		$out .= $this->buildNavigation($paginator);

		return $out;
	}

	protected function buildNavigation(array $paginator)
	{
		$out = '';

		$makeLink = function ($page, $template) {
			$link = $this->settings['uri'];
			if ($page !== 1) {
				$link .= '?page=' . $page;
			}

			return $this->renderTemplate($template, ['link' => $link]);
		};

		$current = $paginator['current'];
		if (!$paginator['isFirst']) {
			$out .= $makeLink($current - 1, 'previous');
		}
		if (!$paginator['isLast']) {
			$out .= $makeLink($current + 1, 'next');
		}

		return $out;
	}

	protected function renderTemplate($template, array $vars = [])
	{
		$template = $this->settings['templates'][$template];
		if (strpos($template, 'file:') === 0) {
			$options = ['base' => $this->settings['templateBasePath']];
			$renderer = $this->getRenderer('file', $options);
			$template = mb_substr($template, 5);
		} else {
			$renderer = $this->getRenderer('string');
			$loader = $renderer->getLoader();
			$loader->setTemplate($template, $template);
		}

		return $renderer->render($template, $vars);
	}

	/**
	 * Get a Twig renderer
	 * 
	 * @param String $type "file" for filesystem or string for array loader
	 * @param Array $options passed as options in Twig loader
	 * 
	 * @return \Twig_Environment Twig render environment
	 */
	protected function getRenderer($type, array $options = [])
	{
		if (isset($this->renderer[$type])) {
			return $this->renderer[$type];
		}
		if ($type === 'file') {
			$loader = new \Twig_Loader_Filesystem($options['base']);
		} else {
			$loader = new \Twig_Loader_Array();
		}
		$this->renderer[$type] = new \Twig_Environment($loader,
			['autoescape' => false]);

		return $this->renderer[$type];
	}

}

<?php
/**
 * Sitemap Helper File
 *
 * Copyright (c) 2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.views.helpers
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Sitemap Helper Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.views.helpers
 */
class SitemapHelper extends AppHelper {

	public $format = 'xml'; // xml or html

	public $helpers = array('Html');

	protected $_data = array();

	public function add($url, $options = array()) {
		$defaults = array(
			'modified' => null,
			'changes' => null, // always, hourly, daily, weekly, monthly, yearly, never
			'priority' => null, // 0.0 - 1.0 (most important), 0.5 is considered the default
			'title' => null,
			'section' => null
		);
		$this->_data[] = compact('url') + $options + $defaults;
	}

	public function generate($options = array()) {
		$defaults = array('reset' => false, 'format' => 'xml');
		extract($options + $defaults);

		if ($format == 'xml') {
			$result = $this->_generateXml();
		} else {
			$result = $this->_generateHtml();
		}
		if ($reset) {
			$this->_data = array();
		}
		return $result;
	}

	protected function _generateHtml() {
		//$html = $this->Html->tag('div', );

		$html = null;
		$sections = array();


		foreach ($this->_data as $item) {
			$sections[$item['section']][] = $item;
		}
		ksort($sections);

		foreach ($sections as $section => $items) {
			$html .= $this->Html->tag('h2', $section);
			$sectionHtml = '';

			foreach ($items as $item) {
				$sectionHtml .= $this->Html->tag('li', $this->Html->link($item['title'], $item['url']));
			}
			$html .= $this->Html->tag('ul', $sectionHtml, array('class' => strtolower(Inflector::slug($section, '-'))));
		}
		return $html;
	}

	protected function _generateXml() {
		$Document = new DomDocument('1.0', 'UTF-8');
		$Set = $Document->createElementNs('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
		$Set->setAttributeNs(
			'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation',
			'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
		);

		foreach ($this->_data as $item) {
			$Url = $Document->createElement('url');

			$Url->appendChild($Document->createElement('loc', h($this->url($item['url'], true))));

			if ($item['modified']) {
				$Url->appendChild($Document->createElement('lastmod', date('c', strftime($item['modified']))));
			}
			if ($item['changes']) {
				$Url->appendChild($Document->createElement('changefreq', $item['changes']));
			}
			if ($item['priority']) {
				$Url->appendChild($Document->createElement('priority', $item['priority']));
			}
			$Set->appendChild($Url);
		}
		$Document->appendChild($Set);

		return $Document->saveXml();
	}

}
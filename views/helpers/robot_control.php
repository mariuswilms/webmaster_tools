<?php
/**
 * RobotControl Helper File
 *
 * Copyright (c) 2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.views.helpers
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * RobotControl Helper Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.views.helpers
 * @link http://www.robotstxt.org/orig.html
 */
class RobotControlHelper extends AppHelper {

	protected $_data = array();

	protected $_directivesOrder = array(
		'User-agent', // 1.0
		'Allow', // 2.0
		'Disallow', // 1.0
		'Sitemap', // nonstandard
		'Crawl-delay', // nonstandard
		'Visit-time', // 2.0
		'Request-rate', // 2.0
		'Comment' // 2.0
	);

	public function allow($url, $agent = '*') {
		$this->_data[$agent]['Allow'][] = $this->url($url);
	}

	public function deny($url, $agent = '*') {
		$this->_data[$agent]['Disallow'][] = $this->url($url);
	}

	public function sitemap($url, $agent = '*') {
		$this->_data[$agent]['Sitemap'][] = $this->url($url, true);
	}

	public function crawlDelay($seconds, $agent = '*') {
		$this->_data[$agent]['Crawl-delay'] = $seconds;
	}

	public function visitTime($from, $until, $agent = '*') {
		$from = date('H:i', strtotime($from));
		$until = date('H:i', strtotime($until));

		$this->_data[$agent]['Visit-time'][] = "{$from} - {$until}";
	}

	// documents per minute
	public function requestRate($documents, $from = null, $until = null, $agent = '*') {
		$data = "{$documents}/60";

		if ($from && $until) {
			$from = date('H:i', strtotime($from));
			$until = date('H:i', strtotime($until));
			$data .= " {$from} - {$until}";
		}
		$this->_data[$agent]['Request-rate'][] = $data;
	}

	public function comment($text, $agent = '*') {
		$this->_data[$agent]['Commet'][] = $text;
	}

	public function generate($options = array()) {
		$defaults = array('reset' => false);
		extract($options += $defaults);

		$output = array();

		$data = $this->_sort($this->_data);

		foreach ($data as $agent => $ruleSet) {
			if ($output) {
				$output[] = "\n";
			}
			$output[] = 'User-agent: ' . $agent;

			foreach ($ruleSet as $directive => $rule) {
				foreach ((array) $rule as $value) {
					$output[] = $directive . ': ' . $value;
				}
			}
			$output[] = '';
		}
		if ($reset) {
			$this->_data = array();
		}
		return implode("\n", $output);
	}

	protected function _sort($data) {
		$sorted = array();

		foreach ($data as $agent => $ruleSet) {
			foreach ($this->_directivesOrder as $directive) {
				if (isset($data[$agent][$directive])) {
					$sorted[$agent][$directive] = $data[$agent][$directive];
				}
			}
		}
		krsort($sorted);
		return $sorted;
	}

	public function url($url, $full = false) {
		$url = parent::url($url, true);

		if (!$full) {
			$url = str_replace(FULL_BASE_URL, '', $url);
		}
		return $url;
	}
}

?>
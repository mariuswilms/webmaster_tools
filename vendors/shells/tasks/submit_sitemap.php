<?php
/**
 * SubmitSitemap Task File
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
 * @subpackage webmaster_tools.shells.tasks
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::import('Core', array('HttpSocket', 'Router'));

/**
 * SubmitSitemap Task Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.shells.tasks
 */
class SubmitSitemapTask extends Shell {

	protected $_Socket;

	protected $_supported = array('google', 'yahoo', 'ask', 'bing');

	public function execute() {
		$this->_Socket = new HttpSocket();

		$sitemap = $this->in('Please enter the sitemap URL: ', null, Router::url(array(
			'plugin' => 'webmaster_tools', 'controller' => 'webmaster_tools',
			'action' => 'sitemap', 'ext' => 'xml'
		), true ));

		$ping = array();

		if ($this->in('Ping all available services?', array('y', 'n'), 'n') == 'y') {
			$ping = $this->_supported;
		} else {
			foreach ($this->_supported as $service) {
				if ($this->in("Ping `{$service}`?", array('y', 'n'), 'n') == 'y') {
					$ping[] = $service;
				}
			}
		}

		$this->out('Sitemap URL: ' . $sitemap);
		$this->out('Services to ping: ' . implode(', ', $ping));

		if ($this->in('Start ping?', array('y', 'n'), 'n') != 'y') {
			return false;
		}

		foreach ($ping as $service) {
			$this->out("Pinging `{$service}`... ", false);
			$method = '_ping' . ucfirst($service);
			$result = $this->{$method}($sitemap);
			$this->out($result ? 'OK' : 'FAILED');
		}
		return true;
	}

	protected function _pingGoogle($sitemap) {
		$url = 'http://www.google.com/webmasters/tools/ping';
		$params = 'sitemap=' . urlencode($sitemap);

		$this->_Socket->get($url, $params);

		if ($this->_Socket->response['status']['code'] != 200) {
			return false;
		}
		return true;
	}

	protected function _pingYahoo($sitemap) {
		$url = 'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification';
		$params = 'appid=' . $this->in('Please enter your yahoo key: ') . '&url=' . urlencode($sitemap);

		$this->_Socket->get($url, $params);

		if ($this->_Socket->response['status']['code'] != 200) {
			return false;
		}
		return true;

	}

	protected function _pingAsk($sitemap) {
		$url = 'http://submissions.ask.com/ping';
		$params = 'sitemap=' . urlencode($sitemap);

		$this->_Socket->get($url, $params);

		if ($this->_Socket->response['status']['code'] != 200) {
			return false;
		}
		return true;
	}

	protected function _pingBing($sitemap) {
		$url = 'http://www.bing.com/webmaster/ping.aspx';
		$params = 'siteMap=' . urlencode($sitemap);

		$this->_Socket->get($url, $params);

		if ($this->_Socket->response['status']['code'] != 200) {
			return false;
		}
		return true;
	}

}

?>
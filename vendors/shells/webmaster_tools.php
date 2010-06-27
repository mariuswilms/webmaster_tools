<?php
/**
 * WebmasterTools Shell File
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
 * @subpackage webmaster_tools.shells
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * WebmasterTools Shell Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.shells
 */
class WebmasterToolsShell extends Shell {

	public $tasks = array('SubmitSitemap');

	public function main() {
		$this->out('[S]ubmit sitemap');
		$this->out('[Q]uit');

		$action = strtoupper($this->in(__('What would you like to do?', true), array('s','q'),'q'));
		switch($action) {
			case 'S':
				$this->SubmitSitemap->execute();
			break;
			case 'Q':
				$this->_stop(0);
			break;
		}
		$this->main();
	}


}

?>
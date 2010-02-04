<?php
/**
 * WebmasterTools Controller File
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
 * @subpackage webmaster_tools.controllers
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * WebmasterTools Controller Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.controllers
 */
class WebmasterToolsController extends WebmasterToolsAppController {

	public $uses = array();

	public $components = array('RequestHandler');

	public function beforeFilter() {
		parent::beforeFilter();

		if (isset($this->Auth)) {
			$this->Auth->allow('sitemap', 'robot_control');
		} elseif (isset($this->Gate)) {
			$this->Gate->Auth->allow('sitemap', 'robot_control');
		}
	}

    public function sitemap(){
		$this->helpers[] = 'WebmasterTools.Sitemap';

        if ($this->RequestHandler->prefers('xml')) {
 			$this->RequestHandler->respondAs('xml');
        }

		$Page = ClassRegistry::init('Page');
		$pages = $Page->find('all');
		$this->set(compact('pages'));
	}

    public function robot_control() {
		$this->helpers[] = 'WebmasterTools.RobotControl';

		$this->RequestHandler->respondAs('txt');
		$this->render('txt/robot_control');
    }

}

?>
<?php
/**
 * Maintenance Component File
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
 * @subpackage webmaster_tools.controllers.components
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Maintenance Component Class
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.controllers.components
 */
class MaintenanceComponent extends Object {

	private $__Controller;

	public function initialize($Controller) {
		$this->__Controller = $Controller;
	}

	/**
	 * Activates maintenance mode.
	 *
	 * Disables debug mode (if activated for i.e. admin) and sets
	 * an appropriate header.
	 *
	 * Example usage:
	 * {{{
	 * if (!$isAdmin && Configure::read('Server.maintenance')) {
	 *	$this->Maintenance->activate();
	 * }
	 * }}}
	 *
	 * @return void
	 * @link http://mark-story.com/posts/view/quick-and-dirty-down-for-maintenance-page-with-cakephp
	 */
	public function activate($message = null) {
		Configure::write('debug', 0);

		$this->__Controller->header('HTTP/1.1 503 Service Temporarily Unavailable');
		$this->__Controller->header('Retry-After: ' . HOUR);

		$this->cakeError('error503', array(
			'code' => 503,
			'base' => $this->__Controller->base,
			'url' => $this->__Controller->here,
			'message' => $message ? $message : __("We're currently working on the site.", true),
			'name' => __('Maintenance', true)
		));
	}
}

?>
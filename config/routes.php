<?php
/**
 * Routes File
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
 * @subpackage webmaster_tools.config
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */

Router::connect('/sitemap', array(
	'plugin' => 'webmaster_tools', 'controller' => 'webmaster_tools', 'action' => 'sitemap'
));
Router::connect('/sitemap.xml', array(
	'plugin' => 'webmaster_tools', 'controller' => 'webmaster_tools', 'action' => 'sitemap'
));
Router::connect('/robots.txt', array(
	'plugin' => 'webmaster_tools', 'controller' => 'webmaster_tools', 'action' => 'robot_control'
));
Router::parseExtensions();
?>
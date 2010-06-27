<?php
/**
 * Plugin Configuration File
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

Configure::write('WebmasterTools.googleAnalytics', array(
	'enable' => true,
	'account' => null,
	'domainName' => null,
	'allowLinker' => null,
	'allowHash' => null
));

?>
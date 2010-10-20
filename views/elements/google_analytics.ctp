<?php
/**
 * Google Analytics Element File
 *
 * Example usage:
 * {{{
 *	echo $this->element('google_analytics', array(
 *		'plugin' => 'webmaster_tools',
 *		'account' => 'UA-XXXXXXXX',
 *		'enable' => env('HTTP_HOST') == 'production.org' && !Configure::read('debug')
 * ));
 * }}}
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
 * @subpackage webmaster_tools.views.elements
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @deprecated
 */
$message  = "The google analytics element has been deprecated. ";
$message .= "Please use the new analytics helper directly.";
trigger_error($message, E_USER_NOTICE);

$defaults = array(
	'enable' => true,
	'account' => null,
	'domainName' => null,
	'allowLinker' => null,
	'allowHash' => null,
	'url' => null
);
extract(Configure::read('WebmasterTools.googleAnalytics') + $defaults, EXTR_SKIP);

if (empty($account)) {
	$message  = "No Google Analytics tracker id found. Provide one to the element directly ";
	$message .= "via the `'account'` key or set it in the configuration as ";
	$message .= "`WebmasterTools.googleAnalytics.account`.";
	trigger_error($message, E_USER_NOTICE);
	$enable = false;
}
?>
<?php if ($enable): ?>
<!-- Google Analytics tracker -->
<?php
	$analytics->config(array_filter(compact(
		'account', 'domainName', 'allowLinker', 'allowHash'
	)));
	$analytics->trackPageview($url);
	echo $analytics->generate();
?>
<?php else: ?>
<!-- Google Analytics tracker omitted (not enabled) -->
<?php endif ?>
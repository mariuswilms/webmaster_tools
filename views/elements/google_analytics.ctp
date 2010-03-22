<?php
/**
 * Google Analytics Element File
 *
 * Example usage:
 * {{{
 *	echo $this->element('google_analytics', array(
 *		'plugin' => 'webmaster_tools',
 *		'tracker' => 'UA-XXXXXXXX',
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
 * CakePHP version 1.2
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.views.elements
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
if (!isset($enable)) {
	$enable = true;
}
if (!isset($tracker)) {
	$tracker = Configure::read('WebmasterTools.googleAnalytics.tracker');
}
if (empty($tracker)) {
	$message  = "No Google Analytics tracker id found. Provide one to the element directly ";
	$message .= "via the `'tracker'` key or set it in the configuration as ";
	$message .= "`WebmasterTools.googleAnalytics.tracker`.";
	trigger_error($message, E_USER_NOTICE);
	$enable = false;
}
?>
<?php if ($enable): ?>
<!-- Google Analytics tracker -->
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape(
		"%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"
	));
</script>
<script type="text/javascript">
	try {
		var pageTracker = _gat._getTracker("<?php echo $tracker; ?>");
		pageTracker._trackPageview();
	} catch(err) {}
</script>
<?php else: ?>
<!-- Google Analytics tracker omitted (not enabled) -->
<?php endif ?>
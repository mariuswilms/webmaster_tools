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
 */
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
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $account ?>']);
<?php if ($domainName): ?>
	_gaq.push(['_setDomainName', "<?php echo $domainName ?>"]);
<?php endif ?>
<?php if ($allowLinker !== null): ?>
	_gaq.push(['_setAllowLinker', <?php echo ($allowLinker ? 'true' : 'false') ?>]);
<?php endif ?>
<?php if ($allowHash !== null): ?>
	_gaq.push(['_setAllowHash', <?php echo ($allowHash ? 'true' : 'false') ?>]);
<?php endif ?>
<?php if ($url): ?>
	_gaq.push(['_trackPageview', '<?php echo $url ?>']);
<?php else: ?>
	_gaq.push(['_trackPageview']);
<?php endif ?>
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
<?php else: ?>
<!-- Google Analytics tracker omitted (not enabled) -->
<?php endif ?>
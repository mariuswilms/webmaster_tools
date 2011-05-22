<?php

class AnalyticsHelper extends AppHelper {

	const CUSTOM_VAR_MAX_LENGTH = 64;
	const CUSTOM_VAR_MAX_INDEX = 5;

	const OPT_SCOPE_VISITOR = 1;
	const OPT_SCOPE_SESSION = 2;
	const OPT_SCOPE_PAGE = 3;

	protected $_commmands = array();

	// If null will use google hosted script.
	protected $_script;

	public function __construct($settings = array()) {
		foreach ((array) $settings as $key => $value) {
			if (property_exists($this, $property = "_{$key}")) {
				$this->{$property} = $value;
			} else {
				call_user_func(array($this, $key), $value);
			}
		}
	}

	public function config($settings) {
		foreach ($settings as $key => $value) {
			call_user_func(array($this, $key), $value);
		}
	}

	/* Options */

	public function script($url) {
		$this->_script = $url;
	}

	public function __call($method, $args) {
		$this->_commands[] = array_merge(array('_set' . ucfirst($method)), $args);
	}

	public function anonymizeIp() {
		$this->_commands[] = array('_gat._anonymizeIp');
	}

	/* Commands */

	public function variable($index, $name, $value, $scope = 'page') {
		if (strlen($name . $value) > self::CUSTOM_VAR_MAX_LENGTH) {
			$message  = 'Analytics::variable - Size of name and value combined exceeds ';
			$message .= self::CUSTOM_VAR_MAX_LENGTH . ' bytes';
			trigger_error($message, E_USER_NOTICE);

			return false;
		}
		if ($index > self::CUSTOM_VAR_MAX_INDEX) {
			$message  = 'Analytics::variable - Index may not be larger then ';
			$message .=  self::CUSTOM_VAR_MAX_INDEX;
			trigger_error($message, E_USER_NOTICE);

			return false;
		}
		$scope = is_string($scope) ? constant('self::OPT_SCOPE_' . strtoupper($scope)) : $scope;

		if ($scope === null) {
			$message  = "Analytics::variable - Unknown scope.";
			trigger_error($message, E_USER_NOTICE);

			return false;
		}
		$this->_commands[] = array('_setCustomVar', $index, $name, $value, $scope);
	}

	public function trackPageview($url = null) {
		$this->_commands[] = $url ? array('_trackPageview', $url) : array('_trackPageview');
	}

	public function trackPageLoadTime() {
		$this->_commands[] = array('_trackPageLoadTime');
	}

	public function trackEvent($category, $action, $label = null, $value = null) {
		$command = array(
			'_trackEvent',
			$category,
			$action
		);
		if (isset($label)) {
			$command[] = $label ? $label : 'undefined';
		}
		if (isset($value)) {
			if (!is_int($value)) {
				$message  = "Analytics::trackEvent - Value is not an integer.";
				trigger_error($message, E_USER_NOTICE);
			}
			$command[] = $value;
		}
		return $this->_renderCommand($command);
	}

	/**
	 * Generates HTML and JavaScript to enable tracking. Will skip generation
	 * if the DNT HTTP header is set and is trueish.
	 *
	 * @param array $options Following options are available:
	 *              -`'reset'`: Resets the commands after generating.
	 * @return string|void The HTML unless a DNT isn't enabled.
	 */
	public function generate(array $options = array()) {
		$options += array(
			'reset' => false
		);

		if ($this->_script) {
			$source = $this->_script;
		} elseif (env('HTTPS')) {
			$source = 'https://ssl.google-analytics.com/ga.js';
		} else {
			$source = 'http://www.google-analytics.com/ga.js';
		 }
		$loader = <<<JS
  (function() {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = '{$source}';

    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
JS;

		$out[] = '<script type="text/javascript">';
		$out[] = '';
		$out[] = '  var _gaq = _gaq || [];';

		foreach ($this->_commands as $command) {
			$out[] = '  ' . $this->_renderCommand($command);
		}

		$out[] = '';
		$out[] = $loader;
		$out[] = '';
		$out[] = '</script>';

		if ($options['reset']) {
			$this->_commands = array();
		}
		if (!env('HTTP_DNT')) {
			return implode("\n", $out);
		}
	}

	protected function _renderCommand($command) {
		return sprintf('_gaq.push(%s);', json_encode($command));
	}
}

?>
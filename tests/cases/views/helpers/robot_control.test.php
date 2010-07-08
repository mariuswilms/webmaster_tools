<?php
/**
 * Robot Control Helper File Test
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
 * @subpackage webmaster_tools.tests.cases.views.helpers
 * @copyright  2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://sitemaps.org/protocol.php
 */
App::import('Helper', 'WebmasterTools.RobotControl');

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://localhost');
}

/**
 * Robot Control Helper Class Test
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.tests.cases.views.helpers
 */
class RobotControlHelperTestCase extends CakeTestCase {

	public $Helper;

	protected $_online;

	public function setUp() {
		$this->Helper = new RobotControlHelper();
		$this->_online = (boolean) @fsockopen('cakephp.org', 80);
	}

	public function testRelativeUrls() {
		$result = $this->Helper->url(array('controller' => 'posts', 'action' => 'add'));
		$this->assertEqual('/posts/add', $result);

		$result = $this->Helper->url('/css');
		$this->assertEqual('/css', $result);

		$result = $this->Helper->url(FULL_BASE_URL . '/css');
		$this->assertEqual('/css', $result);
	}

	public function testUrlsTrailingSlashIsNotTouched() {
		$result = $this->Helper->url('/css/');
		$this->assertEqual('/css/', $result);

		$result = $this->Helper->url('css');
		$this->assertEqual('/css', $result);
	}

	public function testWildcards() {
		$this->Helper->deny('/*q=');
		$this->Helper->deny('/*.atom');
		$this->Helper->deny('/*/raw/');

		$result = $this->Helper->generate();
		$expected = <<<TXT
User-agent: *
Disallow: /*q=
Disallow: /*.atom
Disallow: /*/raw/

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testAllowDenyOrder() {
		$this->Helper->allow('/css/');
		$this->Helper->deny('/secret/');
		$this->Helper->allow('/img/');

		$result = $this->Helper->generate();
		$expected = <<<TXT
User-agent: *
Allow: /css/
Allow: /img/
Disallow: /secret/

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testSitemap() {
		$this->Helper->sitemap('/sitemap.xml');

		$result = $this->Helper->generate();
		$expectedUrl = FULL_BASE_URL . '/sitemap.xml';
		$expected = <<<TXT
User-agent: *
Sitemap: {$expectedUrl}

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testAgentBlocks() {
		$this->Helper->allow('/test0/');
		$this->Helper->allow('/test1/', 'agent0');
		$this->Helper->allow('/test2/');

		$result = $this->Helper->generate(array('reset' => true));
		$expected = <<<TXT
User-agent: agent0
Allow: /test1/

User-agent: *
Allow: /test0/
Allow: /test2/

TXT;
		$this->assertEqual($expected, $result);

		$this->Helper->allow('/test0/', 'agent0');
		$this->Helper->allow('/test1/', 'agent1');
		$this->Helper->allow('/test2/', 'agent2');
		$this->Helper->allow('/test3/');
		$this->Helper->allow('/test4/', 'agent2');

		$result = $this->Helper->generate(array('reset' => true));
		$expected = <<<TXT
User-agent: agent2
Allow: /test2/
Allow: /test4/

User-agent: agent1
Allow: /test1/

User-agent: agent0
Allow: /test0/

User-agent: *
Allow: /test3/

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testCrawlDelay() {
		$this->Helper->crawlDelay(30);

		$result = $this->Helper->generate();
		$expected = <<<TXT
User-agent: *
Crawl-delay: 30

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testVisitTime() {
		$this->Helper->visitTime('13:00', '20:00');

		$result = $this->Helper->generate();
		$expected = <<<TXT
User-agent: *
Visit-time: 13:00 - 20:00

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testRequestRate() {
		$this->Helper->requestRate(20, '13:00', '20:00', 'agent0');
		$this->Helper->requestRate(20, '13:00', null, 'agent1');
		$this->Helper->requestRate(20, null, null, 'agent2');

		$result = $this->Helper->generate();
		$expected = <<<TXT
User-agent: agent2
Request-rate: 20/60

User-agent: agent1
Request-rate: 20/60

User-agent: agent0
Request-rate: 20/60 13:00 - 20:00

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testReset() {
		$this->Helper->allow('/css/');
		$resultA = $this->Helper->generate();
		$resultB = $this->Helper->generate();

		$this->assertEqual($resultA, $resultB);

		$resultA = $this->Helper->generate(array('reset' => true));
		$resultB = $this->Helper->generate();

		$this->assertNotEqual($resultA, $resultB);
	}
}

?>
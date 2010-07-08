<?php
/**
 * Sitemap Helper File Test
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
App::import('Helper', 'WebmasterTools.Sitemap');
App::import('Helper', 'Html');

/**
 * Sitemap Helper Class Test
 *
 * @package    webmaster_tools
 * @subpackage webmaster_tools.tests.cases.views.helpers
 */
class SitemapHelperTestCase extends CakeTestCase {

	public $Helper;

	protected $_online;

	public function setUp() {
		$this->Helper = new SitemapHelper();
		$this->Helper->Html = new HtmlHelper(); // load helper's helpers manually
		$this->_online = (boolean) @fsockopen('cakephp.org', 80);
	}

	public function testSiteindexXml() {
		$skipped  = $this->skipIf(!class_exists('DomDocument'), '%s DomDocument class not available.');
		$skipped |= $this->skipIf(!$this->_online, '%s Not connected to the internet.');

		if ($skipped) {
			return;
		}

		$this->Helper->add(array('controller' => 'site-a', 'action' => 'map', 'ext' => 'xml'), array(
			'title' => 'a map'
		));
		$this->Helper->add(array('controller' => 'site-b', 'action' => 'map', 'ext' => 'xml'), array(
			'title' => 'b map'
		));

		$Document = new DomDocument();
		$Document->loadXml($this->Helper->generate('indexXml'));
		$result = $Document->schemaValidate('http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd');

		$this->assertTrue($result);
	}

	public function testSitemapXml() {
		$skipped  = $this->skipIf(!class_exists('DomDocument'), '%s DomDocument class not available.');
		$skipped |= $this->skipIf(!$this->_online, '%s Not connected to the internet.');

		if ($skipped) {
			return;
		}

		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'index'), array(
			'title' => 'post index'
		));
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'add'), array(
			'title' => 'post add',
			'modified' => 'monthly',
			'priority' => 0.4,
			'section' => 'the section'
		));
		$Document = new DomDocument();
		$Document->loadXml($this->Helper->generate('xml'));
		$result = $Document->schemaValidate('http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

		$this->assertTrue($result);
	}

	public function testSitemapTxt() {
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'index'), array(
			'title' => 'post index'
		));
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'add'), array(
			'title' => 'post add',
			'modified' => 'monthly',
			'priority' => 0.4,
			'section' => 'the section'
		));

		$result = $this->Helper->generate('txt');
		$expected = <<<TXT
/posts-abcdef
/posts-abcdef/add

TXT;
		$this->assertEqual($expected, $result);
	}

	public function testSitemapHtml() {
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'index'), array(
			'title' => 'post index'
		));
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'add'), array(
			'title' => 'post add',
			'modified' => 'monthly',
			'priority' => 0.4,
			'section' => 'the section'
		));

		$result = $this->Helper->generate('html');
		$expected = <<<HTML
<h2></h2><ul class="sitemap"><li><a href="/posts-abcdef">post index</a></li></ul><h2>the section</h2><ul class="sitemap the-section"><li><a href="/posts-abcdef/add">post add</a></li></ul>
HTML;
		$this->assertEqual($expected, $result);
	}

	public function testReset() {
		$this->Helper->add(array('controller' => 'posts-abcdef', 'action' => 'index'), array(
			'title' => 'post index'
		));
		$resultA = $this->Helper->generate('txt');
		$resultB = $this->Helper->generate('txt');

		$this->assertEqual($resultA, $resultB);

		$resultA = $this->Helper->generate('txt', array('reset' => true));
		$resultB = $this->Helper->generate('txt');

		$this->assertNotEqual($resultA, $resultB);
	}
}

?>
<h2>Sitemap</h2>
<?php
$this->set('title_for_layout', 'Sitemap');

foreach ($pages as $page) {
	$url = array('controller' => 'pages', 'action' => 'view', 'slug' => $page['Page']['slug']);
	$sitemap->add($url , array(
		'section' => 'Pages',
		'title' => $page['Page']['title']
	));
}

echo $sitemap->generate(array('format' => 'html'));
?>
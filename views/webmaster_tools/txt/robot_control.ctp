<?php
	$this->layout = 'ajax';

	$robotControl->deny('/');
	echo $robotControl->generate();
?>
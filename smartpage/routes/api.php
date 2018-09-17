<?php
// Non Logged Users
$app->group('/api', function () {
	// Image Server
	$this->map(['GET'], '/test', 'ImageServer:test')
		->setName('test');
		
	$this->map(['GET'], '/img', 'ImageServer:img')
		->setName('img');
		
	$this->map(['GET'], '/min', 'AssetServer:min')
		->setName('min');

});
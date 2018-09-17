<?php

use SmartPage\Dappurware\SPContainer;
use SmartPage\Dappurware\SPUtiles;
use SmartPage\Dappurware\STDGit;
use SmartPage\Dappurware\SPAssets;
use SmartPage\Dappurware\SPSettings;

$container['conf'] = function () use ($container) {
	$root = $container['project_dir'].'/';

	$def = SPUtiles::getFile( '/../../inc/defaults.json');
	$def['app.sp'] = $root.'app/smartpage/views';


	$pat = SPUtiles::getFile( '/../../inc/config.json');
	$dir = SPUtiles::getPath($pat, $root);

	$conf = new SPContainer($def);
	$conf->set('dir', $dir);

	$say = new STDGit();
	$conf->new('gits', $say->say('Its working fine...'));
	$conf->new('dir.root', $root);

	$conf->set(SPSettings::getGlobConf('sp'));
	$conf->set(SPSettings::getGlobConf('server'));


  return $conf;
};

$container['resources'] = function () use ($container) {
	return 'empty';
};

$container['assets'] = function () use ($container) {
	$manager = new SPAssets(['core', 'bootstrap', 'core'], $container['conf']->get('resources'));
	$manager->setBootswatch($container['conf']->get('views.bootswatch.theme'), $container['conf']->get('views.bootswatch.version'));

	$assets = new SPContainer();
	$assets->set($manager->return());

	return $assets;
};

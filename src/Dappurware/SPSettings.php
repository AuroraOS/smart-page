<?php
/**
 * SP Settings Class load all the necessary data from database.
 * Anywhere you need to get some data, call the proper function, and you will get it.
 */
namespace SmartPage\Dappurware;

use SmartPage\Model\SPConfigGroups;
use SmartPage\Model\SPConfig;
use SmartPage\Model\SPModules;
use SmartPage\Model\SPPages;

class SPSettings{

	/**
	 * Load the main Settings Groups from Smart Page.
	 * @method getGlobConf
	 * @return [array]          [Contains all the SP Settings.]
	 */

	public static function getGlobConf(string $type = null){
  	$cfg = [];
  	$config = SPConfigGroups::where('type', '=', $type)->with('config')->get();

  	foreach ($config as $value) {
      	foreach ($value->config as $cfgvalue) {
          	$cfg[$cfgvalue->name] = $cfgvalue->data;
      	}
  	}

		return $cfg;
	}


	public static function getPageData(string $page = null){
		$pageData = SPPages::where('page', '=', 'home')->get();
		$p_cfg = array();
		foreach ($pageData as $pc) {
			$p_cfg['page'] = $pc->page;
			$p_cfg['name'] = $pc->name;
			$p_cfg['tpl'] = $pc->tpl;
			$p_cfg['title'] = $pc->title;
			$p_cfg['keywords'] = $pc->keywords;
			$p_cfg['description'] = $pc->description;
			$p_cfg['page_modules'] = $pc->page_modules;
			$p_cfg['header_type'] = $pc->header_type;
			$p_cfg['image'] = $pc->image;
			$p_cfg['video'] = $pc->video;
			$p_cfg['default'] = $pc->default;
			$p_cfg['plugins'] = $pc->plugins;
		}
		return $p_cfg;
	}

	public static function getPageConf(string $page = null){
  	$cfg = [];
  	$config = SPConfigGroups::where('name', '=', $page)->with('config')->get();

  	foreach ($config as $value) {
      	foreach ($value->config as $cfgvalue) {
          	$cfg[$cfgvalue->name] = $cfgvalue->data;
      	}
  	}

		return $cfg;
	}

	public static function getHeaderObj(string $module = null){
		$cfg = [];
		$modsConfig = SPModules::where('name', '=', 'headers')->get()->first()->toArray();

		foreach ($modsConfig as $key => $value) {
      	foreach ($value as $_key => $cfgvalue) {
          	$cfg[$_key] = $cfgvalue;
      	}
				$cfg[$key] = $value;
  	}

		return $cfg;
	}

	public static function getHeaderConf(string $module = null){
		$cfg = [];
		$config = SPConfigGroups::where('name', '=', 'headers')->get();

		foreach ($config as $value) {
				foreach ($value->config as $cfgvalue) {
						$cfg[$cfgvalue->name] = $cfgvalue->data;
				}
		}

		return $cfg;
	}

}

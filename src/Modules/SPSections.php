<?php
namespace SmartPage\Modules;

use SmartPage\Model\SPConfig;
use SmartPage\Model\SPModules;
use SmartPage\Model\SPPages;

class SPSections{

  public $name = null;
  public $tpl = null;

  public $data = null;
  public $settings = null;
	public $config = null;


  function __construct(string $name, array $opt = null){ //set default to none
    $this->name = $name;
    $this->initSection($opt['page']);
    self::getResources();
    return $this;
  }

  public function initSection(string $page = null){
		$section = SPModules::where('name', '=', $this->name);
    $this->tpl = $section->first()->tpl;
    $this->config = json_decode($section->first()->opt, true);
    $this->settings = json_decode($section->first()->data, true);
		$this->data = json_decode(SPConfig::where('name', '=', 'md-'.$page.'-'.$this->name)->first()->data, true);
		return $this;
	}

  public function getResources(){
		if (isset($this->settings['plugins'])) {
			$this->plugins = $this->settings['plugins'];
			unset($this->settings['plugins']);
		}

		if (isset($this->settings['assets'])) {
			$this->assets = $this->settings['assets'];
			unset($this->settings['assets']);
		}

    if (empty($this->settings)) {
     unset($this->settings);
    }
		return $this;
	}

  public function get(string $key = null){
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		return 'Header property: <b>'.$key.'</b> is not set.';
	}

  public function adminConfig(){
		return $this->config;
	}

  public function getJSONTitles(){
		foreach ($this->data[0] as $_key => $_value) {
			$titles[] = $_key;
		}
		return $titles;
	}

  public function __get($key)
    {//$this->key // returns public->key
        return isset($this->data[$key]) ? $this->data[$key] : false;
    }



    public function __isset($key)
    {
        return isset($this->data[$key]);
    }



}

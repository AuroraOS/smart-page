<?php
namespace SmartPage\Modules;

use SmartPage\Model\SPConfig;
use SmartPage\Model\SPModules;
use SmartPage\Model\SPPages;
use SmartPage\Modules\SPSections;
use SmartPage\Dappurware\SPSettings;
/**
 * This class makes the complett headers...
 */
class SPHeaders{

	protected $name = null;

	
	protected $data = null;
	protected $schema = null;
	protected $values = null;
	

	
	protected $conf = [
		"name" => "headers",
		"tpl" => null
	];


	/** SPHeaders Construct method
	 * --------------------------------------------------
	 * @param string $type [Name of the header's type]
	 * @param string $page [Name of the page]
	 * @param string $ini  [Current page's configuration]
	 */

	function __construct(string $page = null, array $conf = null){ //set default to none	
		$this->name = $page;
		if ($conf) {
			$this->conf = array_merge($this->conf, $conf);
		}
		$this->conf = array_merge($this->conf, SPSettings::getHeaderObj());
		$schema = json_decode($this->conf['opt'], true);
		

		$this->schema = $schema['schema.std-header'];
		$this->values = $schema['values.std-header'];
		$this->data = json_decode($this->conf['data'], true);
		
		unset($this->conf['data']);
		unset($this->conf['opt']);
		unset($this->conf['created_at']);
		unset($this->conf['updated_at']);
		unset($this->conf['id']);
		return $this;
  }

	public function initHeader(string $page = null){
		$header = SPSettings::getHeaderConf();
		return $header;
	}

	public function getMetaData(array $ini = null){
		$this->meta['title'] = $ini['title'];
		$this->meta['description'] = $ini['description'];
		$this->meta['image'] = $ini['image'];
		$this->meta['video'] = $ini['video'];
		return $this;
	}



}

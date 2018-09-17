<?php
namespace SmartPage\Dappurware;
use SmartPage\Dappurware\SPContainer;
class SPAssets{
	private $groups = [];
	private $list = [];

	public function __construct(array $groups = null, array $options = null){
		$this->addGroups($groups);
		$this->setGroups($options);
	
		foreach ($options as $key => $value) {
			foreach ($value as $_key => $_value) {
				if ($_value['enable'] && $_value['async']) {
					foreach ($_value['js'] as $js) {
						$this->list['async.js'][] = $this->checkAsset($js);
					}
					foreach ($_value['css'] as $css) {
						$this->list['async.css'][] = $this->checkAsset($css);
					}
				} else if ($_value['enable'] && !$_value['async']) {
					foreach ($_value['js'] as $js) {
						$this->list['std.js'][] = $this->checkAsset($js);
					}
					foreach ($_value['css'] as $css) {
						$this->list['std.css'][] = $this->checkAsset($css);
					}
				}

				
			
			}
		}
		return $this;
	}
	
	public function return($key = null){
		if($key){
			return $this->list[$key];
		}
		return $this->list;
	}
	
	public function setGroups(array $obj = null){
		foreach ($obj as $key => $value) {
			$this->groups[$key] = $value;
		}
		return $this;
	}
	
	public function addGroups(array $groups = null){
		foreach ($groups as $key => $value) {
			$this->addGroup($value);
		}
	}
	
	private function addGroup(string $name = null){
		$this->groups[$name] = null;
	}
	
	public function setBootswatch(string $theme = "united", string $version = "4"){
		$css = "https://bootswatch.com/".$version."/".$theme."/bootstrap.min.css";
		$this->groups['bootstrap']['twitter_bs']['css'][] = $css;
		$this->list['async.css'][] = $css;
		return $this;
	}
	

  private function checkAsset($path=null, $root = null) {
      if (substr( $path, 0, 4 ) === "http" or substr( $path, 0, 2 ) === '//'){
        return $path;
      } else {
				if ($root) {
					return $path;
				}
        return '/asset?path='. $path;
      }
  }
		
	private function cdn($path=null) {
    if (substr( $path, 0, 4 ) === "http" or substr( $path, 0, 2 ) === '//'){
      return true;
    } else {
      return false;
    }
  }
}

<?php
namespace SmartPage\Dappurware;

class SPContainer{

  private $default	= array(); // For DB / passwords etc
  private $elems		= array(); // For all public strings such as meta stuff for site
	private $cache		= array();

	/**
	 * [__construct description]
	 * This method will populate the default property with default data.
	 * These paramaters are available, it can be overwrite with a new() function, and overwriteable when you try to get() it.]
	 * @param array $config [Contains the default paramaters.]
	 */
	public function __construct(array $config = null) {
		if (is_array($config)) {
			$this->setDefaults($config);
		}
	}

	/**
	 * [set description]
	 * This method will pre set arrays, and populate the elems propeprty.
	 * @param string 			 $key   [description]
	 * @param string|array $value [description]
	 */
  public function set($key = null, $value = null){
		return $this->processDefaultData($key, $value);
  }
	
	/**
	 * [setDefaults description]
	 * This function will store all the default values in the variable: default. When new paramater is used, without a value, these values will be used.
	 * @param array $config [Store all the default values in case of the new paramater is not contains a valid value.]
	 */
	private function setDefaults(array $config = null){
		$this->default = $config;
		return $this;
	}

	/**
	 * [processDefaultData description]
	 * As a set() function can call this private function what has to process the data.
	 * First case scenario: The key is specified, and the values is in array. In that case make a new sub level, and process the values.
	 * Second case scenario: The key is missing, only one paramater presented as an array. In that case this will merge with the default settings.
	 * Third case scenario: Neither statment is true, so the system will return a configuration error message as a paramater.
	 * @param  string|array 		 $_key   [If it is string new subarray will generated.]
	 * @param  array  					 $_value [If this presented and this is an array, the paramaters will merged together with the __constructed paramaters.]
	 * @return object         					 [Return $this as object.s]
	 */
	private function processDefaultData($_key = null, $_value = null){
		/*** First case scenario ***/
		if (is_string($_key) && is_array($_value)) {
			foreach ($_value as $key => $value) {
				$this->new($_key.'.'.$key, $value);
			}
			return $this;
		}
		/*** Second case scenario ***/
		else if (is_array($_key) && !$_value) {
			foreach ($_key as $key => $value) {
				$this->new($key, $value);
			}
			return $this;
		}
		/*** Third case scenario ***/
		else{
			return $this->new('DEF.ERR', 'DEF_ERR: ['.$_key.' OR '.$_value.'] '. 'is invalid.');
		}
  }

	/**
	 * [get description]
	 * This function will return the value of the element is if it is exists.
	 * As a second paramater you can overwrite the previously defined properties.
	 * @param  string 			$key		 [Elem key]
	 * @param  array|string $default [This data will overwrite the previously setted data.]
	 * @return string|array          [If data is not exists, and defult is not set will return the default data in here.]
	 */
	public function get(string $key, $default = null){
	  if (!$default) {
			if ($this->has($key)) {
				return $this->cache[$key];
		  }
			else if ($this->default[$key]) {
				return $this->default[$key];
			}
	  }

	  return $default;
  }

	/**
	 * [all description]
	 * This function will return all the available data in elems. Except default paramaters.
	 * @return array [Elems data]
	 */
	public function all(){
    return $this->elems;
  }

	public function __call($function, $args = null){
		$arr = array();
		if ($args) {
			$args = implode(', ', $args);
			$args = explode(', ', $args);
			foreach ($args as $key => $value) {
				$arr[] = $this->glob($function.'.'.$value);
			}
			return $arr;
		}

		return $arr[$function] = $this->glob($function);
	}

	public function glob($key =  'global'){

		if ($this->has($key)) {
			return $this->get($key);
		} else {
			return 'KEY_ERR: ['.$key.'] '. 'not exists.';
		}
  }

	public function has($key){
        // Check if already cached
        if (isset($this->cache[$key])) {
            return true;
        }

        $segments = explode('.', $key);
        $root = $this->elems;

        // nested case
        foreach ($segments as $segment) {
            if (array_key_exists($segment, $root)) {
                $root = $root[$segment];
                continue;
            } else {
                return false;
            }
        }

        // Set cache for the given key
        $this->cache[$key] = $root;

        return true;
    }

    public function new($key, $value){
				$segs = explode('.', $key);
        $root = &$this->elems;
        $cacheKey = '';

        // Look for the key, creating nested keys if needed
        while ($part = array_shift($segs)) {
            if ($cacheKey != '') {
                $cacheKey .= '.';
            }
            $cacheKey .= $part;
            if (!isset($root[$part]) && count($segs)) {
                $root[$part] = array();
            }
            $root = &$root[$part];

            //Unset all old nested cache
            if (isset($this->cache[$cacheKey])) {
                unset($this->cache[$cacheKey]);
            }
						
						//Unset all old nested cache in case of array
            //if (count($segs) == 0) {
            //    foreach ($this->cache as $cacheLocalKey => $cacheValue) {
            //        if (substr($cacheLocalKey, 0, strlen($cacheKey)) === $cacheKey) {
            //            unset($this->cache[$cacheLocalKey]);
            //        }
            //    }
            //}
        }

        // Assign value at target node
        $this->cache[$key] = $root = $value;
    }



}

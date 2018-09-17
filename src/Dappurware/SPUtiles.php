<?php
namespace SmartPage\Dappurware;

class SPUtiles{
	
	public function getFile(string $path = null, $decode = true, $relative = true){
		if ($relative) {
			$path = __DIR__ . $path;
		}
		
		// Get the list of quotes.
		if (!file_exists($path)) {
			return ["msg.err" => "Searched file not exists: ".$path ];
		}
		$file = file_get_contents( $path );
		// Convert JSON document to PHP array.
		if ($decode) {
			$file = json_decode( $file, true );
		}
		
		return $file;
	}
	
	
	public function getPath($opt, string $root = null){
		if (is_array($opt)) {
			return self::doArray($opt, $root);
		}
		
		return self::doString($opt, $root);
	}
	
	private function doArray($opt, $root = null){
		foreach ($opt as $key => $value) {
			$arr[$key] = $root . $value;
		}
		return $arr;
	}
	
	private function doString($opt, $root = null){
		return $root . $opt;
	}
	
	
	
	public function removeFirst(string $text = null, string $cut = null, string $rep = '') {
		return str_replace($text,$rep,$cut);
	}
		
	public function slugify(string $string, string $c = '-'){
		// replace non letter or digits by -
		$string = preg_replace('~[^\pL\d]+~u', $c, $string);
		// transliterate
		$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
		// remove unwanted characters
		$string = preg_replace('~[^-\w]+~', '', $string);
		// trim
		$string = trim($string, $c);
		// remove duplicate -
		$string = preg_replace('~-+~', $c, $string);
		// lowercase
		$string = strtolower($string);

		if (empty($string)) {
				return false;
		}
	
		return $string;
	}
	
}

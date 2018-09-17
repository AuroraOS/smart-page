<?php
namespace SmartPage\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use \Dappur\Controller\Controller as Controller;
use \Dappur\Dappurware\FileResponse;
use \SmartPage\Dappurware\SPUtiles;

use \Gumlet\ImageResize;

class ImageServer extends Controller{

	protected $container = null;
	protected $response = null;

	private $conf = null;

	private $dir = null;
	private $size = null;
	private $crop = [
		"scale" => "scale",
		"height" => "resizeToHeight",
		"width" => "resizeToWidth",
		"long" => "resizeToLongSide",
		"short" => "resizeToLongSide",
		"fit" => "resizeToBestFit",
		"resize" => "resize",
		"crop" => "crop",
		"cropCenter" => "cropCenter",
		"cropTop" => "cropCenter",
		"cropBottom" => "cropCenter"
	];

	private $def = [
		'dir' => 'uploads',
		'size' => 'preview',
		'crop' => 'fit',
		'cache' => null
	];

	public function __construct(ContainerInterface $container){
		$this->container = $container;
		$this->conf = $this->container['conf'];

		$this->dir['uploads'] = $this->conf->get('dir.uploads');
		$this->dir['app'] = $this->conf->get('dir.app.img');
		$this->dir['headers'] = $this->conf->get('dir.tpl.headers');
		$this->dir['footers'] = $this->conf->get('dir.tpl.footers');
		$this->dir['modules'] = $this->conf->get('dir.tpl.modules');

		$this->def['cache'] = $this->conf->get('server.cache.images');
		$this->size = json_decode($this->conf->get('server.images.sizes'), true);
	}

	private function enabled(string $key = 'server.images.enable'){
		if ($this->conf->get($key)) {
			return true;
		}
		return false;
	}


	public function img(Request $request, Response $response){
		$this->response = $response;

		if (! $this->enabled()) {
			return $response->write('[Access Denied!] SP Image Server does not enabled!');
		}

		if($request->getParam('file')){
			$file = $request->getParam('file');
		}

		if($request->getParam('dir')){
			$dir = $request->getParam('dir');
		}

		if($request->getParam('size')){
			$size = $request->getParam('size');
		}

		if($request->getParam('crop')){
			$crop = $request->getParam('crop');
		}

		$dir = $this->default('dir', $dir);
		$size =  $this->default('size', $size);
		$crop =  $this->default('crop', $crop);


		$name = $this->makeName($file, $size, $crop);
		$cache = $this->conf->get('dir.root').$this->def['cache'].'/'.$name;

		if ($this->checkFile($cache) && $this->enabled('server.images.cache.enable')) {
			$action = 'showCache';
		} else if ($this->checkFile($dir.'/'.$file) && !$this->enabled('server.images.cache.enable')){
			$action = 'resizeOnTheFly';
		} else if ($this->checkFile($dir.'/'.$file) && !$this->enabled('server.images.cache.enable')){
			$action = null;
		} else if ($this->enabled('server.images.cache.enable') && !$this->checkFile($cache)){
			$action = 'resize2Cache';
		}

		if(!$request->getParam('size') && !$request->getParam('crop')){
			$action = null;
		}

		switch ($action) {
			case 'showCache':
				return $this->response->write($action.' Showing cached image...');
				break;

			case 'resizeOnTheFly':
				return $this->resizeImage($dir.'/'.$file, $size, $crop, $dir);
				break;

			case 'resize2Cache':
				return $this->resizeImage($dir.'/'.$file, $size, $crop, $dir);
				break;

			default:
				return $this->returnFile($dir.'/'.$file);
				break;
		}
  }

	private function showOriginal(string $file = null){
		$this->returnFile();
	}


	private function makeName(string $file = null, string $size = null, string $crop = null){
		$i = pathinfo($file);
		$fld = $i['dirname'];
		$fld = str_replace($this->conf->get('dir.root'), "", $i['dirname']);
		$name = SPUtiles::slugify($fld.'/'.$i['filename']);
		return $name.'-'.$size.'-'.SPUtiles::slugify($crop).'.'.$i['extension'];
	}

	private function resizeImage(string $file = null, string $size = null, string $crop = null, string $dir = null){
		$type = image_type_to_mime_type(exif_imagetype($file));

		$image = new ImageResize($file);

		switch ($crop) {
			case 'resizeToBestFit':
				$image->resizeToBestFit($this->size($size, 'width'), $this->size($size, 'height'));
				break;

			case 'scale':
				$image->scale($this->size($size, 'width'));
				break;

			case 'resizeToHeight':
				$image->resizeToHeight($this->size($size, 'height'));
				break;

			case 'resizeToWidth':
				$image->resizeToWidth($this->size($size, 'width'));
				break;

			case 'resizeToLongSide':
				$image->resizeToWidth($this->size($size, 'width'));
			break;

			case 'resizeToShortSide':
				$image->resizeToLongSide($this->size($size, 'width'));
			break;

			case 'resize':
				$image->resize($this->size($size, 'width'), $this->size($size, 'height'), $allow_enlarge = True);
			break;

			case 'crop':
				$image->crop($this->size($size, 'width'), $this->size($size, 'height'));
			break;

			case 'cropCenter':
				$image->crop($this->size($size, 'width'), $this->size($size, 'height'), true, ImageResize::CROPCENTER);
			break;

			case 'cropTop':
				$image->crop($this->size($size, 'width'), $this->size($size, 'height'), true, ImageResize::CROPTOP);
			break;

			case 'cropBottom':
				$image->crop($this->size($size, 'width'), $this->size($size, 'height'), true, ImageResize::CROPBOTTOM);
			break;

			default:
				$image->resizeToBestFit($this->size($size, 'width'), $this->size($size, 'height'));
				break;
		}

		if ($this->enabled('server.images.cache.enable')) {
			$name = $this->makeName($file, $size, $crop);
			$file = $this->conf->get('dir.root').$this->def['cache'].'/'.$name;
			$image->save($file);
			if ($this->checkFile($file)) {
				return $this->returnFile($file);
			}
		} else {
			$out = $image->output();
			return $this->response->withHeader('Content-Type', $type)->write($out);
		}
	}



	private function size(string $size = null, string $return = null){
		$ex_size = explode('x', $size);
		if ($ex_size[0] && $ex_size[1]) {
			if ($return === 'width') {
				return $ex_size[0];
			} else if ($return === 'height'){
				return $ex_size[1];
			}
		} else {
			return $size;
		}
		return $size;
	}

	private function checkFile(string $file = null){
		if (file_exists($file)) {
			return true;
		}
		return false;
	}

	private function returnFile($file){
		if ($this->checkFile($file)) {
			return FileResponse::getResponse($this->response, $file);
		}

		return $this->response->write('Image file can not be showed, cause it is not exists.');
	}

	/**
	 * [Return the default value of the paramater if it is not given.]
	 * @param  string $var   [Name of the properties, what is predefined in the object.]
	 * @param  string $param [Name of the key in the default properties.]
	 * @return string        [Return the given paramaters, or return the default key, and the value.]
	 */
	private function default(string $var = null, string $param = null){
		if (!$param) {
			$param = $this->def[$var];
		}
		if (isset($this->$var[$param])) {
			return $this->$var[$param];
		}
		return $param;
	}

	public function test(Request $request, Response $response){
		echo '<h3>Settings</h3><hr>';
		if ($this->enabled()) {
			$en = '[ ENABLED ]';
		}
		echo '<h5>SP Image Server: '.$en.'</h5>';

		if ($this->enabled('server.images.cache.enabled')) {
			$en = '[ ENABLED ]';
		}
		echo '<h5>SP Image Server Cache: '.$en.'</h5>';



		echo '<h4>Loaded Configs</h4><hr>';
		echo '<pre>';
		print_r($this->conf);
		echo '</pre>';

		echo '<h4>Available Dirs</h4><hr>';
		echo '<pre>';
		print_r($this->dir);
		echo '</pre>';
    return $response->write('<h1>API Controller working well...</h1>');
  }


}

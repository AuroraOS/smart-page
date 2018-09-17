<?php

namespace SmartPage\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use \Dappur\Dappurware\FileResponse;
use \Dappur\Dappurware\Utils;
use Slim\Exception\NotFoundException;
use \Gumlet\ImageResize;
use \MatthiasMullie\Minify;

class Api extends \Dappur\Controller\Controller{
    protected $cache         = true;
    protected $uploads       = NULL;
	protected $project;
    protected $save          = NULL;

    protected $container     = NULL;

    private $img             = NULL;

    public $file             = NULL;
    public $size             = NULL;
    public $opt              = NULL;
		
		/**
     * Test Function to check it out the Api Controller working, or not...
     */
    public function test(Request $request, Response $response)
    {
      return $response->write('API Controller working well...');
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->uploads = $this->container['SPC']['upload_dir'];
		    $this->project = $this->container['SPC']['project_dir'];
        $this->cache =  $this->container['SPC']['project_dir'] . $this->container['SPC']['img_server.cache_dir'];
        $this->save = $this->container['SPC']['image-cache'];
    }

    public function getFileInfo(){
      $info = pathinfo($this->file);
      //$this->img['filename']    = basename($this->file,'.'.$info['name']);
      $this->img['name']        = $info['filename'];
      $this->img['dir']         = $info['dirname'];
      $this->img['ext']         = $info['extension'];
      $this->img['uploads']     = $this->uploads . $this->file;

      $this->img['cache'] = Utils::slugify(
        $this->img['dir'] . '/' .
        $this->img['name'] .
        $this->size['width'] . 'x' . $this->size['height'].
        $this->opt
        ) . '.' . $this->img['ext'];
    }


    public function imgServer(Request $request, Response $response){
      $this->file = $request->getParam('file');

      if($request->getParam('width')){
        $this->size['width']  = $request->getParam('width');
      }
      if($request->getParam('height')){
        $this->size['height']  = $request->getParam('height');
      }
      if($request->getParam('size')){
        $sizes = explode("x", $request->getParam('size'));
        if($sizes[1]){
          $this->size['width']  = $sizes[0];
          $this->size['height'] = $sizes[1];
        } else {
          $this->size  = $request->getParam('size');
        }
      }
      $this->opt = $request->getParam('opt');
      $this->getFileInfo();
        if (is_file($this->img['uploads'])) {
          //return $response->write("File located: ".$this->img['uploads']);
          if(Api::checkCacheFile($this->img['cache'])){
            //return $response->write(" Cache file exists: ".$this->img['cache']);
            return FileResponse::getResponse($response,  $this->cache . $this->img['cache']);
          } else {
            Api::doTheResize($this->img['uploads'], $response);
            if($this->save){
			  //return $response->write(" Cache file exists: ".$this->img['cache']);
              return FileResponse::getResponse($response,  $this->cache . $this->img['cache']);
            } else {
              return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
            }
          }
        } else {
          throw new NotFoundException($request, $response);
        }


        return $response;
    }

    protected function doTheResize($img, Response $response){

      try{
        $image = new ImageResize($img);
      } catch (ImageResizeException $e) {
          throw new \ErrorException("ImageResize Error: ".$e);
      }
      if($this->opt === "crop"){
        $image->crop($this->size['width'], $this->size['height'], ImageResize::CROPCENTER);
      } else if ($this->opt === "cropTop"){
        $image->crop($this->size['width'], $this->size['height'], ImageResize::CROPTOP);
      } else if ($this->opt === "cropBottom"){
        $image->crop($this->size['width'], $this->size['height'], ImageResize::CROPBOTTOM);
      } else if ($this->opt === "resize") {
        $image->resize($this->size['width'], $this->size['height']);
      } else if ($this->opt === "scale") {
        $image->scale($this->size);
      } else if ($this->opt === "bestFit") {
        $image->resizeToBestFit($this->size['width'], $this->size['height']);
      } else if ($this->opt === "toHeight"){
        $image->resizeToHeight($this->size['height']);
      } else if ($this->opt === "toWidth"){
        $image->resizeToWidth($this->size['width']);
      } else {
        $image->resize($this->size['width'], $this->size['height']);
      }

      if($this->save){
        $image->save($this->cache . $this->img['cache']);
      } else {
        $out = $image->output(IMAGETYPE_PNG, 4);
        $response->write($out);

      }

    }

    public function checkCacheFile($name){
      $check_this = $this->cache . $name;
      if(is_file($check_this)){
        return true;
      } else {
        return false;
      }
    }

    public function asset(Request $request, Response $response)
    {

        $assetPath = $this->container['SPC']['upload_dir'].$request->getParam('path');
        // If file doesn't exist
        if (!is_file($assetPath)) {

            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        // If file is in theme root folder
        $regex = '#'.preg_quote($baseThemePath).'(.*)'.preg_quote(DIRECTORY_SEPARATOR).'(.*)#';
        preg_match($regex, $assetPath, $goto_url);
        if (substr_count($goto_url[1], DIRECTORY_SEPARATOR) < 2) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        // Return file
        return FileResponse::getResponse($response, $assetPath);
    }


	// Public function to QRCodess
	public function qrCode(Request $request, Response $response){
		$qrcode = $request->getParam('code');

		$response->getBody()->write($qrcode);

    	return $response;
	}


	// Public function to Minify Assets
	public function open(Request $request, Response $response){

		$assetPath = $request->getParam('path');
        $assetPath = $this->project . str_replace("../", "", $assetPath);



        return FileResponse::getResponse($response, $assetPath);

	}

	public function minAssets(Request $request, Response $response){
    $type = $request->getParam('type');
    $all = $this->container['sp']->assets->get($type, true);
  //  var_dump($all); die();



		foreach ($all as $key => $value) {
			$name = $this->project .'app/views/' . str_replace("../", "", $value);
			//var_dump($name); die();
			$min = $min . file_get_contents($name);
		}
    $response = $response->withHeader("Content-type",'text/'.$type);
  $response = $response->withHeader("Content-Disposition", 'filename="minified"'.$type);
  $response = $response->withHeader("Cache-control", "private");
  //$response = $response->withHeader("Content-length", $size);

    if ($type == 'css') {
      $min= self::minCSS($min);

    return $response->write($min);
    }


        if ($type == 'js') {
          $min= self::minJS($min);
            return FileResponse::getResponse($response, $min);
        }
	}

  public function minCSS($min){
    //$
    //$// Remove comments
  $min = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $min);
  // Remove space after colons
  $min = str_replace(': ', ':', $min);
  // Remove whitespace
  $min = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $min);
  // Enable GZip encoding.
  ob_start("ob_gzhandler");
  return $min;
  }

  public function minJS($min){
    $target = $this->container['SPC']['project_dir'].'/storage/cache/assets/theme.minified.js';
    $minifier = new Minify\JS($min);
    $minifier->minify($target);
// echo
    return $target;
  }


}

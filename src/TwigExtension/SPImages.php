<?php

namespace SmartPage\TwigExtension;

use Psr\Http\Message\RequestInterface;
use Interop\Container\ContainerInterface;

class SPImages extends \Twig_Extension {
	protected $container;
  protected $request;

  public function __construct(ContainerInterface $container) {
    $this->request = $container['request'];
    $this->container = $container;
  }

  public function getName() {
    return 'img';
  }
		
	public function getFunctions() {
    return [
		 	new \Twig_SimpleFunction('img', [$this, 'img']),
			new \Twig_SimpleFunction('src', [$this, 'src'])
    ];
  }


	public function img(string $file = null, string $size = null, string $crop = null, string $class="img-fluid") {
    $out = '<img src="'.$this->src($file, $size, $crop).'" class="'.$class.'" alt="'.$file.'"/>';
		return $out;
  }
	
	public function src(string $file = null, string $size = null, string $crop = null){
		if ($size) {
			$size = '&size='.$size;
		}
		if ($crop) {
			$crop = '&crop='.$crop;
		}
		if ($dir) {
			$dir = '&dir='.$dir;
		}
		return '/api/img?file='.$file.$size.$crop.$dir;
	}

}

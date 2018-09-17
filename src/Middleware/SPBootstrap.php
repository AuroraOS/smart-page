<?php

namespace SmartPage\Middleware;
use Dappur\Middleware\Middleware as Middleware;

/**
 * [SPBootstrap Middleware]
 * -> Később ezt a fájlt innen ki kell venni, és át rakni a csomagomba....!!!!
 */
class SPBootstrap extends Middleware
{
    public function __invoke($request, $response, $next)
    {		
			/**
			 * Add SP Dependencies to the Global Enviroment
			 * @var [$this->container]
			 */
      $this->view->getEnvironment()->addGlobal('conf', $this->conf);
			$this->view->getEnvironment()->addGlobal('resources', $this->resources);
			$this->view->getEnvironment()->addGlobal('assets', $this->assets);
			
			/**
			 * Add SP TwigExtionsons to the Global Enviroment
			 * @var [$this->container]
			 */
			$this->view->addExtension(new \SmartPage\TwigExtension\SPHelpers($this->container['request']));
			$this->view->addExtension(new \SmartPage\TwigExtension\SPImages($this->container));
			
			/** 
			 * [Add Custom Path to TwigView]
			 * @var [type]
			 */
			
			$sp_tpls = $this->container['conf']->get('app.sp');
			$sp_macros = $this->container['conf']->get('dir.tpl.macros');
			$this->view->getEnvironment()->getLoader()->prependPath($sp_tpls, 'sp');
      $this->view->getEnvironment()->getLoader()->prependPath($sp_macros, 'macros');

			
			
			return $next($request, $response);
    }
}
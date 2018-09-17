<?php
namespace SmartPage\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use \Dappur\Controller\Controller as Controller;
use \Dappur\Dappurware\FileResponse;
use \SmartPage\Dappurware\SPUtiles;


class AssetServer extends Controller{

	protected $container = null;
	protected $response = null;

	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}

	public function min(Request $request, Response $response){
		$this->response = $response;
		
		if($request->getParam('group')){
			$group = $request->getParam('group');
		}
		
		return $this->response->write('SP Assets server working fine: '.$group);
	}


}

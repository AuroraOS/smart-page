<?php

namespace SmartPage\Middleware;

use SmartPage\Model\SPConfigGroups;
use SmartPage\Model\SPModules;
use SmartPage\Model\SPConfig;
use SmartPage\Dappurware\SPSettings;
use SmartPage\Dappurware\SPContainer;
use Dappur\Middleware\Middleware as Middleware;


class SPHeadersConfig extends Middleware
{
    public function __invoke($request, $response, $next)
    {


				$modules = new SPContainer();
        $modName = 'headers';

        $modsConfig = SPModules::where('name', '=', $modName)->get();
				$headerData = SPConfig::where('name', '=', 'home.header')->get();
        if ($modsConfig) {
            $cfg = array();
            foreach ($modsConfig as $pc) {
								$json = json_decode($pc->data, true);
								$modules->set('data', json_decode($pc->data, true));
								$modules->set(json_decode($pc->opt, true));


								$modules->set(json_decode($headerData->first()->data, true));

								$modules->new('tpl', $pc->tpl);
								$modules->new('type', $pc->type);
            }
            $this->view->getEnvironment()->addGlobal('header', $modules);
            return $next($request, $response);
        }
        return $next($request, $response);
    }
}

<?php

namespace SmartPage\Middleware;

use SmartPage\Model\SPConfigGroups;
use SmartPage\Model\SPModules;
use SmartPage\Dappurware\SPSettings;
use SmartPage\Dappurware\SPContainer;
use Dappur\Middleware\Middleware as Middleware;


class SPModulesConfig extends Middleware
{
    public function __invoke($request, $response, $next)
    {
				$modules = new SPContainer();
        $modName = 'infoblock';
        
        $modsConfig = SPModules::where('name', '=', $modName)->get();

        if ($modsConfig) {
            $cfg = array();
            foreach ($modsConfig as $pc) {
								$json = json_decode($pc->data, true);
								$modules->new($pc->name.'.tpl', $pc->tpl);
								$modules->new($pc->name.'.data', json_decode($pc->data, true));
								$modules->new($pc->name.'.opt', json_decode($pc->opt, true));
								$modules->new($pc->name.'.func', $pc->func);
								$modules->new($pc->name.'.type', $pc->type);
            }
            
            $this->view->getEnvironment()->addGlobal('module', $modules);
            return $next($request, $response);
        }
        
        return $next($request, $response);
    }
}

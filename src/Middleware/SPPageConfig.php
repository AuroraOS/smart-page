<?php

namespace SmartPage\Middleware;

use SmartPage\Model\SPConfigGroups;

use SmartPage\Dappurware\SPSettings;
use SmartPage\Dappurware\SPContainer;
use Dappur\Middleware\Middleware as Middleware;


class SPPageConfig extends Middleware
{
    public function __invoke($request, $response, $next)
    {



			$page = new SPContainer();

			$pageConf = SPSettings::getPageData($pageName);
			$page->new('conf', $pageConf);

			$pageData = SPSettings::getPageConf($pageName);
			$page->new('page', $pageData);

      if ($pageConf || $pageData) {
          $this->view->getEnvironment()->addGlobal('page', $page);
      }

      return $next($request, $response);
    }
}

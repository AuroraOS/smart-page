<?php

// Global Middlewares go here
$app->add(new SmartPage\Middleware\SPHeadersConfig($container));
$app->add(new SmartPage\Middleware\SPModulesConfig($container));
$app->add(new SmartPage\Middleware\SPBootstrap($container));
$app->add(new SmartPage\Middleware\SPPageConfig($container));

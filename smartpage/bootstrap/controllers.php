<?php


$container['ImageServer'] = function ($container) {
    return new \SmartPage\Controller\ImageServer($container);
};

$container['AssetServer'] = function ($container) {
    return new \SmartPage\Controller\AssetServer($container);
};

$container['Api'] = function ($container) {
    return new \SmartPage\Controller\Api($container);
};
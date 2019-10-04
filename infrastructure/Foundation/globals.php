<?php

use Infrastructure\Router;

function generateLink(string $uri = '') {
    return Router::generateLink($uri);
}
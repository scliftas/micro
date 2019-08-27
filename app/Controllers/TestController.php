<?php

namespace App\Controllers;

use Infrastructure\View;

class TestController
{
    public function hello()
    {
        return View::render('index', [
            'hello' => 'Hello',
            'world' => 'World!',
        ]);
    }
}
<?php

namespace Infrastructure;

class View
{
    /**
     * Render and return the given view file
     *
     * @param string $view View file to render
     * @param array $vars Variables to include in the view
     * @return void
     */
    public static function render(string $view, array $vars = []): void
    {
        extract($vars);

        require_once __DIR__ . '/../../app/Views/' . $view . '.view.php';
        
        exit();
    }
}
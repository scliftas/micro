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

    /**
     * Parse and return the given view file as a string
     *
     * @param string $view View file to parse
     * @param array $vars Variables to include in the view
     * @return string
     */
    public static function parse(string $view, array $vars = []): string
    {
        extract($vars);

        // Enable output buffering and include the view, storing the result of the buffer into a variable
        ob_start();
        include(__DIR__ . '/../../app/Views/' . $view . '.view.php');
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
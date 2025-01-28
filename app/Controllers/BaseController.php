<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;

class BaseController
{
    /**
     * Load a view and return its output as a string.
     *
     * @param string $viewPath
     * @param array $data
     * @return string
     */
    protected function loadView($viewPath, $data = [])
    {
        extract($data, EXTR_SKIP);
        ob_start();

        $viewFile = __DIR__ . "/../Views/{$viewPath}.php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View file '{$viewPath}' not found.";
        }

        return ob_get_clean();
    }

    /**
     * Load a layout and render the page.
     *
     * @param string $layoutPath
     * @param array $data
     */
    protected function loadLayout($layoutPath, $data = [])
    {
        extract($data, EXTR_SKIP);

        $layoutFile = __DIR__ . "/../Views/{$layoutPath}.php";

        if (file_exists($layoutFile)) {
            require_once $layoutFile;
        } else {
            echo "Layout file '{$layoutPath}' not found.";
        }
    }
}

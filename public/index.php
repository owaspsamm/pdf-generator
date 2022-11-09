<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

!defined('ELFINDER_IMAGEMAGICK_PS') && define('ELFINDER_IMAGEMAGICK_PS', false);

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

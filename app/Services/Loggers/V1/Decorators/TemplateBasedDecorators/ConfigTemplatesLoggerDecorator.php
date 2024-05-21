<?php

namespace App\Services\Loggers\V1\Decorators\TemplateBasedDecorators;


use App\Services\Loggers\V1\Decorators\BaseDecorators\TemplateBasedLoggerDecorator;

class ConfigTemplatesLoggerDecorator extends TemplateBasedLoggerDecorator
{
    public function getTemplate(): string
    {
        return config('services.loggers.v1.templates.config');
    }
}

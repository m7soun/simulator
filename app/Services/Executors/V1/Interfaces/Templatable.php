<?php

namespace App\Services\Executors\V1\Interfaces;
interface Templatable
{
    public function getTemplate(): string|null;

    public function setTemplate(string|null $template = null): void;
}

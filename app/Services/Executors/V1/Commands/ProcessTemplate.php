<?php

namespace App\Services\Executors\V1\Commands;

use App\Services\Executors\V1\Interfaces\Command;
use App\Services\Executors\V1\Interfaces\Templatable;
use App\Services\Simulators\V1\Entities\Interfaces\Drivers\Driver;

class ProcessTemplate implements Command
{

    public function __construct(private Templatable $entity)
    {
    }

    public function execute()
    {
        error_log("PID : " . getmypid() . " - Processing template for " . $this->entity->getDriverId());

        $functionInfo = extractFunctionName($this->entity->getTemplate());
        $updatedTemplate = $this->entity->getTemplate();

        foreach ($functionInfo as $info) {
            $functionName = $info['functionName'];
            $args = $info['args'];

            if ($args[0] === "driver" && $this->entity instanceof Driver) {
                $args = $this->entity;
            }

            if (function_exists($functionName)) {
                $result = call_user_func_array($functionName, [$args]);
                $updatedTemplate = replacePlaceholderInTemplate($updatedTemplate, $info['fullMatch'], $result);
            }
        }
        return $updatedTemplate;
    }
}

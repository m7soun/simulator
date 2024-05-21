<?php

namespace App\Services\Simulators\V1\Facades;

use App\Services\Simulators\V1\Actions\Interfaces\Action;
use App\Services\Simulators\V1\Entities\Interfaces\Entity;
use App\Services\Simulators\V1\Entities\Interfaces\Threadable;
use App\Services\Threads\V1\Adapters\Adaptee\PcntlThread;

class Simulation
{
    private $stateManager;

    public function __construct(private Entity $entity, $stateManager)
    {
        $this->stateManager = $stateManager;
        $this->run();
    }

    private function run()
    {
        sleep(rand(0, config('services.simulator.v1.movement.defaults.sleep')));
        $dubaiTimeZone = new \DateTimeZone('Asia/Dubai');

        $shiftStartTime = $this->entity->getShiftStart();
        $shiftEndTime = $this->entity->getShiftEnd();

        $now = new \DateTime('now', $dubaiTimeZone);
        $shiftStart = new \DateTime($now->format('Y-m-d') . ' ' . $shiftStartTime, $dubaiTimeZone);
        $shiftEnd = new \DateTime($now->format('Y-m-d') . ' ' . $shiftEndTime, $dubaiTimeZone);

        $actions = $this->entity->getActions();
        while ($now >= $shiftStart && $now <= $shiftEnd) {
            foreach ($actions as $action) {
                $action = new $action($this->entity);
                if ($action instanceof Action) {
                    if ($action->isThreadable()) {
                        if ($this->entity instanceof Threadable) {
                            $thread = new PcntlThread($action->getThreadable());
                            $thread->setKey($this->entity->getThreadKey());
                            $thread->run($this->entity, $action);
                        } else {
                            throw new \Exception('Entity must be instance of ' . Threadable::class . ' interface as the action is threadable');
                        }
                    } else {
                        $action->execute();
                    }
                } else {
                    throw new \Exception('Action must be instance of ' . Action::class . ' interface');
                }
            }
        }

        error_log("PID : " . getmypid() . " - driver " . $this->entity->getDriverId() . " is not in shift time");
        exit(0);
    }
}

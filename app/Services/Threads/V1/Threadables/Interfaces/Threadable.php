<?php

namespace App\Services\Threads\V1\Threadables\Interfaces;

use App\Services\Threads\V1\Adapters\Interfaces\Thread;

interface Threadable
{
    public function run($args);

    public function setThread(Thread $thread);
}

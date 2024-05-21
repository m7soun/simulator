<?php

namespace App\Repositories\V1\Drivers;

use App\Models\V1\Drivers\Driver;
use App\Repositories\V1\BaseRepository;

class GetDriversByTeamId extends BaseRepository
{
    public function setModel(): void
    {
        $this->model = new Driver();
    }

    public function run(?array $data = [])
    {
        return $this->model->with('user')->whereIn('teams', $data)->get();
    }
}

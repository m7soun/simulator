<?php

namespace App\Services\Simulators\V1\Entities\Drivers\Abstractions;

abstract class Driver
{
    protected array $driverDetails;
    protected int $driverId;
    protected string $name;
    protected string $loginUsername;
    protected string $loginPassword;
    protected string|null $accessToken = null;
    protected string|array $entityName;
    protected array $driverLoginResponse;
    protected array $driverPickup;
    protected string $imsi;
    protected array $shiftOnResponse = [];
    protected int $clientId;
    protected float $latitude = 0;
    protected float $longitude = 0;
    protected float $speed;
    protected string $status;

    protected float $pickupLatitude;
    protected float $pickupLongitude;

    protected string $refreshToken;
    protected int $refreshTokenExpiresIn;
    protected array $refreshTokenApiResponse;

    protected bool $isMoving = false;


    const STATUS_AVAILABLE = 'AVAILABLE';
    const STATUS_NOT_AVAILABLE = 'NOT_AVAILABLE';
    const STATUS_ASSIGNED = 'ASSIGNED';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_RETURNING = 'RETURNING';

    public function getDriverDetails(): array
    {
        return $this->driverDetails;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function getLoginUsername(): string
    {
        return $this->loginUsername;
    }

    public function getLoginPassword(): string
    {
        return $this->loginPassword;
    }

    public function setDriverDetails(array $driverDetails): void
    {
        $this->driverDetails = $driverDetails;
    }

    public function setDriverId(int $driverId): void
    {
        $this->driverId = $driverId;
    }

    public function setLoginUsername(string $loginUsername): void
    {
        $this->loginUsername = $loginUsername;
    }

    public function setLoginPassword(string $loginPassword): void
    {
        $this->loginPassword = $loginPassword;
    }

    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function setImsi(string $imsi): void
    {
        $this->imsi = $imsi;
    }

    public function getImsi(): string
    {
        return $this->imsi;
    }

    public function setDriverLoginResponse(array $response): void
    {
        $this->driverLoginResponse = $response;
    }

    public function getDriverLoginResponse(): array
    {
        return $this->driverLoginResponse;
    }

    public function setDriverPickup(array $response): void
    {
        $this->driverPickup = $response;
    }

    public function getDriverPickup(): array
    {
        return $this->driverPickup;
    }

    public function setAccessToken(string $accessToken): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('access_token', $accessToken);
            }
        }
        $this->accessToken = $accessToken;
    }

    public function getAccessToken(): string|null
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $accessToken = $this->getSharedMemory('access_token');
                if (!is_null($accessToken)) {
                    $this->accessToken = $accessToken;
                    return $accessToken;
                }
            }
        }
        return $this->accessToken;
    }

    public function setShiftOnResponse(array $response): void
    {
        $this->shiftOnResponse = $response;
    }

    public function getShiftOnResponse(): array
    {
        return $this->shiftOnResponse;
    }

    public function getDriverName(): string
    {
        return $this->name;
    }

    public function setDriverName(string $name): void
    {
        $this->name = $name;
    }

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function setLatitude(float $latitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('latitude', $latitude);
            }
        }
        $this->latitude = $latitude;
    }

    public function getLatitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $latitude = $this->getSharedMemory('latitude');
                if (!is_null($latitude)) {
                    $this->latitude = $latitude;
                    return $latitude;
                }
            }
        }
        return $this->latitude;
    }

    public function setLongitude(float $longitude): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('longitude', $longitude);
            }
        }
        $this->longitude = $longitude;
    }

    public function getLongitude(): float
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $longitude = $this->getSharedMemory('longitude');
                if (!is_null($longitude)) {
                    $this->longitude = $longitude;
                    return $longitude;
                }
            }
        }
        return $this->longitude;
    }

    public function setSpeed(float $speed): void
    {
        $this->speed = $speed;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function setStatus(string $status): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('status', $status);
            }
        }
        $this->status = $status;
    }

    public function getStatus(): string
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $status = $this->getSharedMemory('status');
                if (!is_null($status)) {
                    $this->status = $status;
                    return $status;
                }
            }
        }
        return $this->status;
    }

    public function setDriverPickupLatitude(float $latitude): void
    {
        $this->pickupLatitude = $latitude;
    }

    public function getDriverPickupLatitude(): float
    {
        return $this->pickupLatitude;
    }

    public function setDriverPickupLongitude(float $longitude): void
    {
        $this->pickupLongitude = $longitude;
    }

    public function getDriverPickupLongitude(): float
    {
        return $this->pickupLongitude;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('refresh_token', $refreshToken);
            }
        }
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $refreshToken = $this->getSharedMemory('refresh_token');
                if (!is_null($refreshToken)) {
                    $this->refreshToken = $refreshToken;
                    return $refreshToken;
                }
            }
        }
        return $this->refreshToken;
    }

    public function setRefreshTokenExpiresIn(int $refreshTokenExpiresIn): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('refresh_token_expires_in', $refreshTokenExpiresIn);
            }
        }
        $this->refreshTokenExpiresIn = $refreshTokenExpiresIn;
    }

    public function getRefreshTokenExpiresIn(): int
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $refreshTokenExpiresIn = $this->getSharedMemory('refresh_token_expires_in');
                if (!is_null($refreshTokenExpiresIn)) {
                    $this->refreshTokenExpiresIn = $refreshTokenExpiresIn;
                    return $refreshTokenExpiresIn;
                }
            }
        }
        return $this->refreshTokenExpiresIn;
    }

    public function setRefreshTokenApiResponse(array $response): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $this->setSharedMemory('refresh_token_api_response', json_encode($response));
            }
        }
        $this->refreshTokenApiResponse = $response;
    }

    public function getRefreshTokenApiResponse(): array
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this)) && $this->isSharedMemoryEnabled()) {
                $refreshTokenApiResponse = $this->getSharedMemory('refresh_token_api_response');
                if (!is_null($refreshTokenApiResponse)) {
                    $this->refreshTokenApiResponse = json_decode($refreshTokenApiResponse, true);
                    return json_decode($refreshTokenApiResponse, true);
                }
            }
        }
        return $this->refreshTokenApiResponse;
    }

    public function getShiftStart(): string
    {
        return config('services.simulator.v1.drivers.defaults.drivers.basic.shift_start');
    }

    public function getShiftEnd(): string
    {
        return config('services.simulator.v1.drivers.defaults.drivers.basic.shift_end');
    }


    public function isLoggedIn(): bool
    {
        return !is_null($this->getAccessToken());
    }

    public function isShiftOn(): bool
    {
        return !count($this->getShiftOnResponse()) == 0;
    }

    public function isMoving(): bool
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $isMoving = $this->getSharedMemory('is_moving');
                if (!is_null($isMoving)) {
                    $this->isMoving = $isMoving;
                    return $isMoving;
                }
            }
        }
        return $this->isMoving;
    }

    public function setIsMoving(bool $isMoving): void
    {
        if (trait_exists('App\Services\Simulators\V1\Entities\Traits\SharedMemory')) {
            if (in_array('App\Services\Simulators\V1\Entities\Traits\SharedMemory', class_uses($this))) {
                $this->setSharedMemory('is_moving', $isMoving);
            }
        }
        $this->isMoving = $isMoving;
    }
}

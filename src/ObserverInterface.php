<?php

declare(strict_types=1);

namespace Miquido\Observable;

interface ObserverInterface
{
    public function next($data): void;

    public function complete(): void;
}

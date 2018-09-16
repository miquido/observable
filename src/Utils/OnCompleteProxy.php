<?php

declare(strict_types=1);

namespace Miquido\Observable\Utils;

use Miquido\Observable\ObserverInterface;

final class OnCompleteProxy
{
    /**
     * @var ObserverInterface
     */
    private $observer;

    public function __construct(ObserverInterface $observer)
    {
        $this->observer = $observer;
    }

    public function __invoke(): void
    {
        $this->observer->complete();
    }
}

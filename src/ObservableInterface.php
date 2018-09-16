<?php

declare(strict_types=1);

namespace Miquido\Observable;

interface ObservableInterface
{
    public function pipe(OperatorInterface $operator): self;

    /**
     * @param ObserverInterface|callable $observer
     */
    public function subscribe($observer): void;
}

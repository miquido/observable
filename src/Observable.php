<?php

declare(strict_types=1);

namespace Miquido\Observable;

final class Observable implements ObservableInterface
{
    /**
     * @var callable
     */
    private $onSubscribe;

    public function __construct(callable $onSubscribe)
    {
        $this->onSubscribe = $onSubscribe;
    }

    public function subscribe($observer): void
    {
        \call_user_func($this->onSubscribe, Observer::create($observer));
    }

    public function pipe(OperatorInterface $operator): ObservableInterface
    {
        return $operator->process($this);
    }
}

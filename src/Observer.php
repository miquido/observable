<?php

declare(strict_types=1);

namespace Miquido\Observable;

final class Observer implements ObserverInterface
{
    /**
     * @var callable
     */
    private $onNext;

    /**
     * @var callable
     */
    private $onComplete;

    public static function create($observer): ObserverInterface
    {
        if (\is_callable($observer)) {
            $observer = new Observer($observer);
        }

        if (!$observer instanceof ObserverInterface) {
            throw new \InvalidArgumentException(\sprintf(
                'Only callable or ObserverInterface are acceptable, got "%s".',
                \is_object($observer) ? \get_class($observer) : \gettype($observer)
            ));
        }

        return $observer;
    }

    public function __construct(callable $onNext = null, callable $onComplete = null)
    {
        $this->onNext = $onNext;
        $this->onComplete = $onComplete;
    }

    public function next($data): void
    {
        if ($this->onNext) {
            \call_user_func($this->onNext, $data);
        }
    }

    public function complete(): void
    {
        if ($this->onComplete) {
            \call_user_func($this->onComplete);
        }
    }
}

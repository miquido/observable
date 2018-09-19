<?php

declare(strict_types=1);

namespace Miquido\Observable\Subject;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;

final class Subject implements ObserverInterface, ObservableInterface
{
    /**
     * @var ObserverInterface[]
     */
    private $observers = [];

    public function pipe(OperatorInterface $operator): ObservableInterface
    {
        return $operator->process($this);
    }

    public function subscribe($observer): void
    {
        $this->observers[] = Observer::create($observer);
    }

    public function next($data): void
    {
        foreach ($this->observers as $observer) {
            $observer->next($data);
        }
    }

    public function complete(): void
    {
        foreach ($this->observers as $observer) {
            $observer->complete();
        }
        $this->observers = [];
    }

    public function asObservable(): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer): void {
            $this->subscribe(new Observer(
                function ($data) use ($observer): void {
                    $observer->next($data);
                },
                function () use ($observer): void {
                    $observer->complete();
                }
            ));
        });
    }
}

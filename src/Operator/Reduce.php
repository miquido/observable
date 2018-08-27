<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;

final class Reduce implements OperatorInterface
{
    /**
     * @var callable
     */
    private $callback;
    private $initial;

    public function __construct(callable $callback, $initial)
    {
        $this->callback = $callback;
        $this->initial = $initial;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $acc = $this->initial;
            $source->subscribe(new Observer(function ($data) use (&$acc): void {
                $acc = \call_user_func($this->callback, $acc, $data);
            }, function () use ($observer, &$acc): void {
                $observer->next($acc);
                $observer->complete();
            }));
        });
    }
}

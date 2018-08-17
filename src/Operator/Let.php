<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Utils\OnCompleteProxy;
use Miquido\Observable\OperatorInterface;

final class Let implements OperatorInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $source->subscribe(new Observer(
                function($data) use ($observer): void {
                    \call_user_func($this->callback, $data);
                    $observer->next($data);
                },
                new OnCompleteProxy($observer)
            ));
        });
    }
}

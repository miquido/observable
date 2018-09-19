<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;
use Miquido\Observable\Utils\OnCompleteProxy;

final class Flat implements OperatorInterface
{
    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $source->subscribe(new Observer(
                function ($data) use ($observer): void {
                    foreach ((array) $data as $item) {
                        $observer->next($item);
                    }
                },
                new OnCompleteProxy($observer)
            ));
        });
    }
}

<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Utils\OnCompleteProxy;
use Miquido\Observable\OperatorInterface;

final class Flat implements OperatorInterface
{
    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source) {
            $source->subscribe(new Observer(
                function (array $data) use ($observer) {
                    foreach ($data as $item) {
                        $observer->next($item);
                    }
                },
                new OnCompleteProxy($observer)
            ));
        });
    }
}

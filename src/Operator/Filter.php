<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;
use Miquido\Observable\Utils\OnCompleteProxy;

final class Filter implements OperatorInterface
{
    /**
     * @var callable
     */
    private $filter;

    public function __construct(callable $filter)
    {
        $this->filter = $filter;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $source->subscribe(new Observer(
                function ($data) use ($observer): void {
                    if (\call_user_func($this->filter, $data)) {
                        $observer->next($data);
                    }
                },
                new OnCompleteProxy($observer)
            ));
        });
    }
}

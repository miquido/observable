<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;

final class Skip implements OperatorInterface
{
    /**
     * @var int
     */
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $lastValue = null;
            $skipped = 0;

            $source->subscribe(new Observer(function ($data) use ($observer, &$lastValue, &$skipped): void {
                $lastValue = $data;
                ++$skipped;

                if ($skipped === $this->count) {
                    $skipped = 0;
                    $observer->next($lastValue);
                }
            }, function () use ($observer, &$lastValue, &$skipped): void {
                if ($skipped > 0) {
                    $observer->next($lastValue);
                }
                $observer->complete();
            }));
        });
    }
}

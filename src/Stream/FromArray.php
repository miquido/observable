<?php

declare(strict_types=1);

namespace Miquido\Observable\Stream;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\ObserverInterface;

final class FromArray
{
    public static function create(array $data): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($data): void {
            foreach ($data as $item) {
                $observer->next($item);
            }
            $observer->complete();
        });
    }
}

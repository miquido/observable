<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\ObservableInterface;
use Miquido\Observable\OperatorInterface;

final class Count implements OperatorInterface
{
    public function process(ObservableInterface $source): ObservableInterface
    {
        return $source->pipe(new Reduce(function (int $carry): int {
            return $carry + 1;
        }, 0));
    }
}

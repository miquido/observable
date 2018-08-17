<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\ObservableInterface;
use Miquido\Observable\OperatorInterface;

final class ArrayCount implements OperatorInterface
{

    public function process(ObservableInterface $source): ObservableInterface
    {
        return $source->pipe(new Map(function (array $data): int {
            return \count($data);
        }));
    }
}

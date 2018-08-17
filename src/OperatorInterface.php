<?php

declare(strict_types=1);

namespace Miquido\Observable;

interface OperatorInterface
{
    public function process(ObservableInterface $source): ObservableInterface;
}

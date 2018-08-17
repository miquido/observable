<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;

final class Buffer implements OperatorInterface
{
    /**
     * @var ObservableInterface
     */
    private $toggle;

    public function __construct(ObservableInterface $toggle)
    {
        $this->toggle = $toggle;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $buffer = [];
            $completed = false;
            $complete = function () use ($observer, &$buffer, &$completed) {
                if (!$completed) {
                    if (\count($buffer)) {
                        $observer->next($buffer);
                    }
                    $observer->complete();
                    $buffer = [];
                    $completed = true;
                }
            };
            $source->subscribe(new Observer(function ($data) use (&$buffer) {
                $buffer[] = $data;
            }, $complete));

            $this->toggle->subscribe(new Observer(function () use ($observer, &$buffer) {
                $data = $buffer;
                $observer->next($data);
                $buffer = [];
            }, $complete));
        });
    }
}

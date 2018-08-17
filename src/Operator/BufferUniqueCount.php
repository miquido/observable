<?php

declare(strict_types=1);

namespace Miquido\Observable\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\ObservableInterface;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\OperatorInterface;

final class BufferUniqueCount implements OperatorInterface
{
    /**
     * @var int
     */
    private $bufferCount;

    /**
     * @var bool
     */
    private $releaseOnComplete;

    public function __construct(int $bufferCount, bool $releaseOnComplete = true)
    {
        $this->bufferCount = $bufferCount;
        $this->releaseOnComplete = $releaseOnComplete;
    }

    public function process(ObservableInterface $source): ObservableInterface
    {
        return new Observable(function (ObserverInterface $observer) use ($source): void {
            $buffer = [];
            $source->subscribe(new Observer(
                function ($data) use ($observer, &$buffer): void {
                    if (!\in_array($data, $buffer, true)) {
                        $buffer[] = $data;
                    }
                    if (\count($buffer) === $this->bufferCount) {
                        $observer->next($buffer);
                        $buffer = [];
                    }
                },
                function () use ($observer, &$buffer) {
                    if ($this->releaseOnComplete && \count($buffer)) {
                        $observer->next($buffer);
                        $buffer = [];
                    }
                    $observer->complete();
                }
            ));
        });
    }
}

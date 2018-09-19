<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\BufferUniqueCount;
use PHPUnit\Framework\TestCase;

final class BufferUniqueCountTest extends TestCase
{
    public function testBufferUniqueCountOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(3))->method('__invoke')->withConsecutive([[1, 2, 3]], [[3, 4, 2]], [[1]]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            foreach ([1, 1, 2, 2, 3, 3, 3, 4, 2, 1] as $i) {
                $observer->next($i);
            }
            $observer->complete();
        });

        $observable->pipe(new BufferUniqueCount(3))->subscribe(new Observer($onNext, $onComplete));
    }

    public function testBufferUniqueCountOperator_NoReleaseOnComplete(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(2))->method('__invoke')->withConsecutive([[1, 2, 3]], [[3, 4, 2]]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            foreach ([1, 1, 2, 2, 3, 3, 3, 4, 2, 1] as $i) {
                $observer->next($i);
            }
            $observer->complete();
        });

        $observable->pipe(new BufferUniqueCount(3, false))->subscribe(new Observer($onNext, $onComplete));
    }
}

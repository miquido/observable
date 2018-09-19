<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\BufferCount;
use PHPUnit\Framework\TestCase;

final class BufferCountTest extends TestCase
{
    public function testBufferCountOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(4))->method('__invoke')->withConsecutive([[1, 2, 3]], [[4, 5, 6]], [[7, 8, 9]], [[10]]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $i) {
                $observer->next($i);
            }
            $observer->complete();
        });

        $observable->pipe(new BufferCount(3))->subscribe(new Observer($onNext, $onComplete));
    }

    public function testBufferCountOperator_NoReleaseOnComplete(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(3))->method('__invoke')->withConsecutive([[1, 2, 3]], [[4, 5, 6]], [[7, 8, 9]]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $i) {
                $observer->next($i);
            }
            $observer->complete();
        });

        $observable->pipe(new BufferCount(3, false))->subscribe(new Observer($onNext, $onComplete));
    }
}

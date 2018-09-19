<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Reduce;
use PHPUnit\Framework\TestCase;

final class ReduceTest extends TestCase
{
    public function testReduce(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->once())->method('__invoke')->withConsecutive([15]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next(1);
            $observer->next(2);
            $observer->next(3);
            $observer->next(4);
            $observer->next(5);
            $observer->complete();
        });

        $observable
            ->pipe(new Reduce(function (int $accumulate, int $value): int {
                return $accumulate + $value;
            }, 0))
            ->subscribe(new Observer($onNext, $onComplete));
    }
}

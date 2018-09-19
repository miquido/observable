<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Flat;
use PHPUnit\Framework\TestCase;

final class FlatTest extends TestCase
{
    public function testFlatOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(10))->method('__invoke')->withConsecutive([1], [2], [3], [4], [5], [6], [7], [8], [9], [10]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next([1, 2, 3]);
            $observer->next([4, 5, 6]);
            $observer->next(7);
            $observer->next(8);
            $observer->next([9, 10]);
            $observer->complete();
        });

        $observable->pipe(new Flat())->subscribe(new Observer($onNext, $onComplete));
    }
}

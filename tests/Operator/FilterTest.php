<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Filter;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function testFilterOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(3))->method('__invoke')->withConsecutive([3], [6], [9]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next(1);
            $observer->next(2);
            $observer->next(3);
            $observer->next(4);
            $observer->next(5);
            $observer->next(6);
            $observer->next(7);
            $observer->next(8);
            $observer->next(9);
            $observer->next(10);
            $observer->complete();
        });

        $observable
            ->pipe(new Filter(function (int $number): bool {
                return $number % 3 === 0;
            }))
            ->subscribe(new Observer($onNext, $onComplete));
    }
}

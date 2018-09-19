<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Count;
use PHPUnit\Framework\TestCase;

final class CountTest extends TestCase
{
    public function testCountOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->once())->method('__invoke')->withConsecutive([5]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next(1);
            $observer->next(1);
            $observer->next(1);
            $observer->next(1);
            $observer->next(1);
            $observer->complete();
        });

        $observable->pipe(new Count())->subscribe(new Observer($onNext, $onComplete));
    }
}

<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\ArrayCount;
use PHPUnit\Framework\TestCase;

final class ArrayCountTest extends TestCase
{
    public function testArrayCountOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->once())->method('__invoke')->with(5);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next([1, 2, 3, 4, 5,]);
            $observer->complete();
        });
        $observable->pipe(new ArrayCount())->subscribe(new Observer($onNext, $onComplete));
    }
}

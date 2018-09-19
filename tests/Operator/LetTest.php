<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Let;
use PHPUnit\Framework\TestCase;

final class LetTest extends TestCase
{
    public function testLetOperator(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(5))->method('__invoke')->withConsecutive([1], [2], [3], [4], [5]);

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
            ->pipe(new Let(function (int $value): int {
                return $value * 10;  // returned value will be ignored, subscriber will receive original value
            }))
            ->subscribe(new Observer($onNext, $onComplete));
    }
}

<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Operator;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Map;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testMap(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(5))->method('__invoke')->withConsecutive([1], [4], [9], [16], [25]);

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
            ->pipe(new Map(function (int $value): int {
                return $value * $value;
            }))
            ->subscribe(new Observer($onNext, $onComplete));
    }
}

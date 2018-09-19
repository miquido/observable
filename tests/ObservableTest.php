<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use Miquido\Observable\ObserverInterface;
use Miquido\Observable\Operator\Map;
use PHPUnit\Framework\TestCase;

final class ObservableTest extends TestCase
{
    public function testObservable(): void
    {
        $observer1 = new Observer();
        $observer2 = new Observer();
        $onSubscribeMock = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onSubscribeMock
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                [$this->identicalTo($observer1)],
                [$this->identicalTo($observer2)]
            );

        $observable = new Observable($onSubscribeMock);
        $observable->subscribe($observer1);
        $observable->subscribe($observer2);
    }

    public function testPipe(): void
    {
        $observable = new Observable(function (ObserverInterface $observer): void {
            $observer->next(1);
            $observer->next(16);
            $observer->next(81);
            $observer->complete();
        });
        $observable = $observable->pipe(new Map('sqrt'))->pipe(new Map('sqrt'));

        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(3))->method('__invoke')->withConsecutive([1], [2], [3]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $observer = new Observer($onNext, $onComplete);

        $observable->subscribe($observer);
    }
}

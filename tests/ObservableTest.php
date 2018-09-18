<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests;

use Miquido\Observable\Observable;
use Miquido\Observable\Observer;
use PHPUnit\Framework\TestCase;

final class ObservableTest extends TestCase
{
    public function testObservable(): void
    {
        $observer1 = new Observer();
        $observer2 = new Observer();
        $onSubscribeMock = $this->getMockBuilder(\stdClass::class)->setMethods(['onSubscribe'])->getMock();
        $onSubscribeMock
            ->expects($this->exactly(2))
            ->method('onSubscribe')
            ->withConsecutive(
                [$this->identicalTo($observer1)],
                [$this->identicalTo($observer2)]
            );

        $observable = new Observable([$onSubscribeMock, 'onSubscribe']);
        $observable->subscribe($observer1);
        $observable->subscribe($observer2);
    }
}

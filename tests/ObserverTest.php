<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests;

use Miquido\Observable\Observer;
use PHPUnit\Framework\TestCase;

final class ObserverTest extends TestCase
{
    public function testStaticCreationShouldReturnObserverWhenCallbackWasGiven(): void
    {
        $callbackMock = $this->getMockBuilder(\stdClass::class)->setMethods(['onNext'])->getMock();
        $callbackMock->expects($this->once())->method('onNext')->with($this->equalTo(1));

        $observer = Observer::create([$callbackMock, 'onNext']);
        $observer->next(1);
    }

    public function testStaticCreationShouldReturnSameObjectWhenObserverWasGiven(): void
    {
        $observerInput = new Observer();

        $observer = Observer::create($observerInput);

        $this->assertSame($observer, $observerInput);
    }

    public function testStaticCreationShouldFailWhenNeitherCallbackOrObserverWasGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only callable or ObserverInterface are acceptable, got "integer".');

        Observer::create(123);
    }

    public function testCallbacksShouldBeInvoked(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['onNext'])->getMock();
        $onNext->expects($this->exactly(3))->method('onNext')->withConsecutive([1], [2], [3]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['onComplete'])->getMock();
        $onComplete->expects($this->once())->method('onComplete');

        $observer = new Observer([$onNext, 'onNext'], [$onComplete, 'onComplete']);
        $observer->next(1);
        $observer->next(2);
        $observer->next(3);
        $observer->complete();
    }
}

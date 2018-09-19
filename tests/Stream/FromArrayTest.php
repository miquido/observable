<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Stream;

use Miquido\Observable\Observer;
use Miquido\Observable\Stream\FromArray;
use PHPUnit\Framework\TestCase;

final class FromArrayTest extends TestCase
{
    public function testFromArray(): void
    {
        $observable = FromArray::create([1, 2, 3, 4, 5]);

        $onNextMock = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNextMock->expects($this->exactly(5))->method('__invoke')->withConsecutive([1], [2], [3], [4], [5]);

        $onCompleteMock = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onCompleteMock->expects($this->once())->method('__invoke');

        $observer = new Observer($onNextMock, $onCompleteMock);

        $observable->subscribe($observer);
    }
}

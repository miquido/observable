<?php

declare(strict_types=1);

namespace Miquido\Observable\Tests\Subject;

use Miquido\Observable\Observer;
use Miquido\Observable\Operator\Map;
use Miquido\Observable\Subject\Subject;
use PHPUnit\Framework\TestCase;

final class SubjectTest extends TestCase
{
    public function testSubject(): void
    {
        $onNext = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext->expects($this->exactly(3))->method('__invoke')->withConsecutive([1], [2], [3]);

        $onComplete = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onComplete->expects($this->once())->method('__invoke');

        $subject = new Subject();
        $subject->subscribe(new Observer($onNext, $onComplete));
        $subject->next(1);
        $subject->next(2);
        $subject->next(3);
        $subject->complete();
        $subject->complete(); // second complete should not be passed to observer
    }

    public function testSubjectPipesAreImmutable(): void
    {
        $onNext1 = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext1->expects($this->exactly(3))->method('__invoke')->withConsecutive([1], [2], [3]);

        $onNext2 = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext2->expects($this->exactly(3))->method('__invoke')->withConsecutive([10], [20], [30]);

        $onNext3 = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $onNext3->expects($this->exactly(3))->method('__invoke')->withConsecutive([100], [200], [300]);

        $subject = new Subject();
        $subject->asObservable()->subscribe($onNext1);
        $subject2 = $subject->pipe(new Map(function (int $i): int { return $i * 10; }));
        $subject2->subscribe($onNext2);
        $subject2->pipe(new Map(function (int $i): int { return $i * 10; }))->subscribe($onNext3);

        $subject->next(1);
        $subject->next(2);
        $subject->next(3);
        $subject->complete();
    }
}

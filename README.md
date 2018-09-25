[![Build](https://travis-ci.org/miquido/observable.svg?branch=develop)](https://travis-ci.org/miquido/observable)
[![Maintainability](https://api.codeclimate.com/v1/badges/e60a93e53ddde8a3875c/maintainability)](https://codeclimate.com/github/miquido/observable/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/e60a93e53ddde8a3875c/test_coverage)](https://codeclimate.com/github/miquido/observable/test_coverage)
[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)

# Observable

Set of classes for data streams.

- [Installation guide](#installation)
- [Examples](#examples)
- [Contributing](#contributing)

## Installation 
Use [Composer](https://getcomposer.org) to install the package:

```shell
composer require miquido/observable
```

## Examples
- [Create and subscribe to a data stream](#create-and-subscribe-to-a-data-stream)
- [Manipulate data in a stream with *Operators*](#manipulate-data-in-a-stream-with-operators)
- [Using a *Subject*](#using-a-subject)
- [List of build-in operators](#list-of-build-in-operators)

### Create and subscribe to a data stream

You can start with simple *Miquido\Observable\Stream\FromArray::create* method.

```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Observer;

// create an observable stream from an array:
// $stream is an objects that implements Miquido\Observable\ObservableInterface
$stream = FromArray::create([1, 2, 3, 4, 5]);

// then you can subscribe to the stream using Observer (both parameters are optional):
$stream->subscribe(new Observer(
    function (int $i): void { /* this callback will be called 5 times with consecutive 1, 2, 3, 4, 5*/ }, 
    function (): void { /* this callback will be called once after every items in the stream will be emitted */ }
));

// alternatively - if you are only interested in items in the stream you can pass just a callback 
$stream->subscribe(function (int $i): void {
    // do something with numbers
});

```

### Manipulate data in a stream with *Operators*

Operators can be useful when you want to process the data in the stream before notifying observers.
Operators do not interfere with source stream, every operator returns new stream that can be subscribed independently.

```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);

$squareStream = $stream->pipe(new Operator\Map(function (int $i): int {
    return $i * $i;
}));

$sumStream = $stream->pipe(new Operator\Sum());
$squareSumStream = $squareStream->pipe(new Operator\Sum());
$tripleSumStream = $stream
    ->pipe(new Operator\Map(function (int $i): int {
        return $i ** 3;
    }))
    ->pipe(new Operator\Filter(function (int $i): bool {
        return $i % 3 > 0;
    }));

$squareStream->subscribe(function ($i) {}); // called 5 times with consecutive: 1, 4, 9, 16, 25
$squareSumStream->subscribe(function ($i) {}); // called once with a number 55
$sumStream->subscribe(function ($i) {}); // called once with a number 15
```

You can also add multiple pipes:

```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;


$stream = FromArray::create([1, 2, 3, 4, 5, 6]);
$stream
    // first pipe raises each number to a power 3,
    ->pipe(new Operator\Map(function (int $i): int {
        return $i ** 3;
    }))
    // then BufferCount(3) receives stream of numbers: 1, 8, 25, 64, 125, 216
    // holds the stream until it receives 3 values, then releases array with three values
    ->pipe(new Operator\BufferCount(3))
    // next pipes receives two arrays of three numbers: [1, 8, 27], [64, 125, 216] and returns sum of each group
    ->pipe(new Operator\Map(function (array $numbers): int {
        return array_sum($numbers);
    }))
    ->subscribe(function (int $i): void {
        // subscribe() is called twice with numbers: 36, 405
    });
```

### Using a *Subject*
Subject acts both as an observer and as an observable. See an example below:
```php
<?php

use Miquido\Observable\Subject\Subject;
use Miquido\Observable\Operator;

// lets create a Subject
$words = new Subject();

// because it is an observable, you can pipe and subscribe to the data
$words
    ->pipe(new Operator\Map(function (string $word): string {
        return \strtoupper($word);
    }))
    ->subscribe(function (string $word): void {
        // receives upper cased words
    });

$words
    ->pipe(new Operator\Map('ucfirst'))
    ->pipe(new Operator\Reduce(
        function (string $sentenceInProgress, string $word) {
            return \sprintf('%s %s', $sentenceInProgress, $word);
        },
        ''
    ))
    ->pipe(new Operator\Map('trim'))
    ->subscribe(function (string $sentence): void {
        // receives a sentence of all words int the stream
    });

// And because a Subject is also an observer, you can push new items to the stream.
$words->next('lorem');
$words->next('ipsum');
$words->next('dolor');
$words->next('sit');
$words->next('amet');

// complete will send a "complete" notification to all observers and will remove observers from the subject
$words->complete();

/**
 * In this example:
 * - first subscriber will receive 5 items: 'LOREM', 'IPSUM', 'DOLOR', 'SIT' and 'AMET'
 * - second subscriber will recive one item: 'Lorem Ipsum Dolor Sit Amet'
 */

```

### List of build-in operators
- [ArrayCount](#arraycount-operator)
- [BufferCount](#buffercount-operator)
- [BufferUniqueCount](#bufferuniquecount-operator)
- [Count](#count-operator)
- [Filter](#filter-operator)
- [Flat](#flat-operator)
- [Flat](#let-operator)
- [Reduce](#reduce-operator)
- [Scan](#scan-operator)
- [Sum](#sum-operator)

### *ArrayCount* operator
Transforms array item to number with count value.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([
    [1, 2],
    [3, 4, 5] 
]);
$stream
    ->pipe(new Operator\ArrayCount())
    ->subscribe(function (int $count): void {
        // called twice with values: 2 and 3
    });
```

### *BufferCount* operator
Groups individual items into an array of provided size.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5, 6]);
$stream
    ->pipe(new Operator\BufferCount(3))
    ->subscribe(function (array $values): void {
        // called twice with values: [1, 2, 3] and [4, 5, 6]
    });
```

### *BufferUniqueCount* operator
Similar to *BufferCount*, but removes duplications.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 1, 2, 1, 3, 4, 5, 5, 6]);
$stream
    ->pipe(new Operator\BufferUniqueCount(3))
    ->subscribe(function (array $values): void {
        // called twice with values: [1, 2, 3] and [4, 5, 6]
    });
```

### *Count* operator
Count all items emitted into the stream.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 1, 2, 1, 3, 4, 5, 5, 6]);
$stream
    ->pipe(new Operator\Count())
    ->subscribe(function (int $count): void {
        // called once with value: 9
    });
```

### *Filter* operator
Removes all values for which provided callback returns false.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);
$stream
    ->pipe(new Operator\Filter(function (int $number): bool {
        return $number % 2 === 0;
    }))
    ->subscribe(function (int $number): void {
        // called twice with values: 2, 4
    });
```

### *Flat* operator
If item in a stream is an array, *Flat* converts this array into set of individual items. 
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([
    [1, 2],
    [3, 4, 5]
]);
$stream
    ->pipe(new Operator\Flat())
    ->subscribe(function (int $number): void {
        // called 5 times with values: 1, 2, 3, 4, 5
        var_dump($number);
    });
```

### *Let* operator
Does nothing, do the stream, just fires provided callback for every item in the stream and returns unchanged value.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);
$stream
    ->pipe(new Operator\Let(function (int $number): void {
        // do something with this number, no need to return anything
    }))
    ->subscribe(function (int $number): void {
        // called 5 times with values: 1, 2, 3, 4, 5
        var_dump($number);
    });
```

### *Map* operator
Transform each item in the stream into new value.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create(['lorem', 'ipsum', 'dolor', 'sit', 'amet']);
$stream
    ->pipe(new Operator\Map(function (string $word): int {
        return \strlen($word);
    }))
    ->subscribe(function (int $length): void {
        // called 5 times with values: 5, 5, 5, 3, 5
    });
```
### *Reduce* operator
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);
$stream
    ->pipe(new Operator\Reduce(
        function (int $sum, int $number): int {
            return $sum + $number;
        },
        0
    ))
    ->subscribe(function (int $sum): void {
        // called once with value 15
    });
```
### *Scan* operator
Like *Reduce*, but observer receives a value after each Scan call.
```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);
$stream
    ->pipe(new Operator\Scan(
        function (int $sum, int $number): int {
            return $sum + $number;
        },
        0
    ))
    ->subscribe(function (int $sum): void {
        // called 5 times with values: 1, 3, 6, 10, 15
    });
```
### *Sum* operator
Sums all items in the stream.

```php
<?php

use Miquido\Observable\Stream\FromArray;
use Miquido\Observable\Operator;

$stream = FromArray::create([1, 2, 3, 4, 5]);
$stream
    ->pipe(new Operator\Sum())
    ->subscribe(function (int $sum): void {
        // called once with value 15
    });
```

## Contributing

Pull requests, bug fixes and issue reports are welcome.
Before proposing a change, please discuss your change by raising an issue.

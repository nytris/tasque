# Tasque

[![Build Status](https://github.com/nytris/tasque/workflows/CI/badge.svg)](https://github.com/nytris/tasque/actions?query=workflow%3ACI)

[EXPERIMENTAL] Run PHP background [green threads][2] concurrently.

> Note that these are not true threads: instead they are ["green threads"][2] as they still run in the single main PHP process' primary OS thread,
> inside a separate [Fiber][3] for each, being interrupted by the Tasque scheduler when it is time to context switch either to the next background thread
> or back to the main thread.
> 
> This is why we use the term "concurrent" rather than "parallel".

## Why?
To allow periodic background tasks, such as sending keep-alive or heartbeat messages,
to be performed in a traditional PHP environment where there is no event loop.

## Usage
Install this package with Composer:

```shell
$ composer install tasque/tasque
```

Configure Nytris platform:

`nytris.config.php`

```php
<?php

declare(strict_types=1);

use Nytris\Boot\BootConfig;
use Nytris\Boot\PlatformConfig;
use Tasque\Tasque;

$bootConfig = new BootConfig(new PlatformConfig(__DIR__ . '/var/cache/nytris/'));

$bootConfig->installPackage(Tasque::class);

return $bootConfig;
```

### Starting a thread

`index.php`

```php
<?php

declare(strict_types=1);

use Tasque\Tasque;

require_once __DIR__ . '/vendor/autoload.php';

$tasque = new Tasque();

class MyMainThreadLogic
{
    public function getFirst(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'first'; 
    }

    public function getSecond(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'second'; 
    }

    public function getThird(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'third'; 
    }
}

class MyBackgroundThreadLogic
{
    public function getFirst(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'first'; 
    }

    public function getSecond(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'second'; 
    }

    public function getThird(): string
    {
        print __METHOD__ . PHP_EOL;

        return 'third'; 
    }
}

$thread = $tasque->createThread(
    function () {
        $bgThreadLogic = new MyBackgroundThreadLogic;

        return $bgThreadLogic->getFirst() . ' ' . $bgThreadLogic->getSecond() . ' ' . $bgThreadLogic->getThird(); 
    }
);
$thread->start();

$mainThreadLogic = new MyMainThreadLogic;

$mainThreadResult = $mainThreadLogic->getFirst() . ' ' . $mainThreadLogic->getSecond() . ' ' . $mainThreadLogic->getThird();

// Wait for the background thread to complete and capture its result.
$thread->join();
$bgThreadResult = $thread->getReturn();

print 'Main thread result: "' . $mainThreadResult . '"';
print 'Background thread result: "' . $bgThreadResult . '"';
```

## Limitations

- Threads can only be handled once `Tasque` itself has been able to initialise.

## See also
- [PHP Code Shift][1]

[1]: https://github.com/asmblah/php-code-shift
[2]: https://en.wikipedia.org/wiki/Green_thread
[3]: https://www.php.net/manual/en/language.fibers.php

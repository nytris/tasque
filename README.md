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

## Demos

- See the [Tasque demo][4] for an example of how Tasque is used to start multiple threads.
- See the [Tasque EventLoop demo][5] to see how [Tasque EventLoop][6] can be used
  to run a ReactPHP event loop inside one of those threads, concurrently with the main thread
  (and any other Tasque background threads).

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
use Tasque\TasquePackage;

$bootConfig = new BootConfig(new PlatformConfig(__DIR__ . '/var/cache/nytris/'));

$bootConfig->installPackage(new TasquePackage());

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

$thread = $tasque->createThread(
    function () {
        /*
         * Run your background thread logic here.
         *
         * Note that this should be outside of the entrypoint script for your application,
         * so that PHP Code Shift can transpile it with the tock calls required by Tasque.
         */
    }
);
$thread->start();

// Wait for the background thread to complete and capture its result.
$thread->join();
$backgroundThreadResult = $thread->getReturn();

print 'Background thread result: "' . $backgroundThreadResult . '"';
```

### Terminating a thread

A background thread can be terminated from outside, e.g.:

```php
<?php

declare(strict_types=1);

use Tasque\Tasque;

// ...

$tasque = new Tasque();

$thread = $tasque->createThread(
    function () {
        // ...
    }
);
$thread->start();

// ...

// Terminate the background thread, leaving the current thread still running,
// which may be the main thread or another background thread.
$thread->terminate();
```

or from inside:

```php
<?php

declare(strict_types=1);

use Tasque\Core\Thread\Control\InternalControlInterface;
use Tasque\Tasque;

// ...

$tasque = new Tasque();

$thread = $tasque->createThread(
    function (InternalControlInterface $threadControl) {
        // ...

        // Terminate this background thread. No further logic will execute inside it.
        $threadControl->terminate();

        // Any code here will not be reached.
    }
);
$thread->start();
```

## Limitations

- Threads can only be handled once `Tasque` itself has been able to initialise.

## See also
- [PHP Code Shift][1]

[1]: https://github.com/asmblah/php-code-shift
[2]: https://en.wikipedia.org/wiki/Green_thread
[3]: https://www.php.net/manual/en/language.fibers.php
[4]: https://github.com/nytris/tasque-demo
[5]: https://github.com/nytris/tasque-event-loop-demo
[6]: https://github.com/nytris/tasque-event-loop

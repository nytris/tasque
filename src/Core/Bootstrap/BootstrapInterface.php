<?php

/*
 * Tasque - Run PHP background green threads concurrently.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/tasque/
 *
 * Released under the MIT license.
 * https://github.com/nytris/tasque/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Tasque\Core\Bootstrap;

use Tasque\TasquePackageInterface;

/**
 * Interface BootstrapInterface.
 *
 * Bootstraps Tasque.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BootstrapInterface
{
    /**
     * Installs Tasque.
     */
    public function install(TasquePackageInterface $packageConfig): void;

    /**
     * Determines whether Tasque has been installed or not.
     */
    public function isInstalled(): bool;

    /**
     * Uninstalls Tasque.
     */
    public function uninstall(): void;
}

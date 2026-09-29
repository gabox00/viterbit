<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\Command;

interface ICommandBus
{
    public function dispatch(ICommand $command): void;
}

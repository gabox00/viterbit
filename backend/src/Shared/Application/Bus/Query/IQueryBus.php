<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\Query;

interface IQueryBus
{
    /**
     * @template TResponse
     *
     * @param IQuery<TResponse> $query
     *
     * @return TResponse
     */
    public function ask(IQuery $query): mixed;
}

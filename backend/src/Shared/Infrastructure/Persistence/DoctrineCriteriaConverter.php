<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Enum\FilterOperatorEnum;
use App\Shared\Domain\Criteria\Filter;
use Doctrine\ORM\QueryBuilder;

final readonly class DoctrineCriteriaConverter
{
    /** @param array<string, list<string>> $pathsByField DQL paths (alias.property) each criteria field can match */
    public function __construct(private array $pathsByField)
    {
    }

    public function applyFilters(QueryBuilder $queryBuilder, Criteria $criteria): QueryBuilder
    {
        foreach ($criteria->filters as $index => $filter) {
            $parameter = 'filter_'.$index;
            $conditions = array_map(
                fn (string $path): string => $this->condition($path, $filter->operator, $parameter),
                $this->pathsOf($filter->field),
            );

            $queryBuilder
                ->andWhere($queryBuilder->expr()->orX(...$conditions))
                ->setParameter($parameter, $this->parameterValue($filter));
        }

        return $queryBuilder;
    }

    public function apply(QueryBuilder $queryBuilder, Criteria $criteria): QueryBuilder
    {
        $this->applyFilters($queryBuilder, $criteria);

        if (null !== $criteria->order) {
            $queryBuilder->orderBy($this->pathsOf($criteria->order->field)[0], $criteria->order->direction->value);
        }

        return $queryBuilder
            ->setMaxResults($criteria->limit)
            ->setFirstResult($criteria->offset ?? 0);
    }

    /** @return list<string> */
    private function pathsOf(string $field): array
    {
        return $this->pathsByField[$field] ?? throw new \InvalidArgumentException(sprintf('Field "%s" cannot be used in criteria.', $field));
    }

    private function condition(string $path, FilterOperatorEnum $operator, string $parameter): string
    {
        return match ($operator) {
            FilterOperatorEnum::Equal => sprintf('%s = :%s', $path, $parameter),
            // Case-insensitive: PostgreSQL's LIKE is case-sensitive
            FilterOperatorEnum::Contains => sprintf("LOWER(%s) LIKE LOWER(:%s) ESCAPE '\\'", $path, $parameter),
        };
    }

    private function parameterValue(Filter $filter): string
    {
        return match ($filter->operator) {
            FilterOperatorEnum::Equal => $filter->value,
            FilterOperatorEnum::Contains => '%'.addcslashes($filter->value, '%_\\').'%',
        };
    }
}

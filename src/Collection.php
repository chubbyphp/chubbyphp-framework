<?php

declare(strict_types=1);

namespace Chubbyphp\Framework;

/**
 * @template-covariant T
 */
final class Collection
{
    /**
     * @var array<T>
     */
    private array $items = [];

    /**
     * @param array<T>      $items
     * @param array<string> $types
     */
    public function __construct(array $items, array $types)
    {
        foreach ($items as $i => $item) {
            foreach ($types as $type) {
                if ($item instanceof $type) {
                    $this->items[$i] = $item;

                    continue 2;
                }
            }

            throw new \TypeError(
                \sprintf(
                    '%s::__construct() expects parameter 1 at index %s to be %s, %s given',
                    self::class,
                    $i,
                    implode('|', $types),
                    $item::class
                )
            );
        }
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}

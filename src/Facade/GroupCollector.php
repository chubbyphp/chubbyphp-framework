<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Facade;

use Chubbyphp\Framework\Router\GroupInterface;
use Chubbyphp\Framework\Router\RouteInterface;

final class GroupCollector extends AbstractCollector
{
    public static function create(): self
    {
        return new self([]);
    }

    /**
     * @return list<GroupInterface|RouteInterface>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param list<GroupInterface|RouteInterface> $children
     */
    protected function withChildren(array $children): static
    {
        return new self($children);
    }
}

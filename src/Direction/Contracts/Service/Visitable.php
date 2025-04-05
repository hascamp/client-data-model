<?php

namespace Hascamp\Direction\Contracts\Service;

use Closure;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;

interface Visitable
{
    public function setVisitor(): void;
    public function setVisited(): void;

    public function getVisitor(): ?Visitor;
    public function getVisited(): ?Visited;

    public function headers(): Closure;
    public function hasVisit(): bool;
}
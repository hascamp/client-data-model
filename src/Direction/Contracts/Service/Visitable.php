<?php

namespace Hascamp\Direction\Contracts\Service;

use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Builder\Factory\AssetRequestFactory;

interface Visitable
{
    public function setVisitor(): void;
    public function setVisited(): void;

    public function getAssetFactory(): AssetRequestFactory;
    public function getVisitor(): ?Visitor;
    public function getVisited(): ?Visited;
}
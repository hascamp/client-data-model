<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Client\Models\User;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Builder\DataVisited;
use Hascamp\Direction\Builder\DataVisitor;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Builder\Factory\AssetRequestFactory;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

class Visit implements Visitable
{
    /** @var \Hascamp\Direction\Builder\Factory\AssetRequestFactory */
    private $assetFactory;

    /** @var \Hascamp\Direction\Contracts\Visitor */
    protected $visitor;

    /** @var \Hascamp\Direction\Contracts\Visited */
    protected $visited;

    public function __construct(
        BasePlatform $app,
        private ?User $user,
        string $routeName,
    )
    {
        $factory = new AssetRequestFactory;
        $this->assetFactory = $factory($app, $routeName);
    }

    public function setVisitor(): void
    {
        $this->visitor = DataVisitor::from([
            'hspid' => $this->user->hspid ?? null,
            'name' => $this->user->name ?? null,
            'username' => $this->user->username ?? null,
        ]);
    }

    public function setVisited(): void
    {
        $this->visited = DataVisited::from([
            'target' => null,
            'id' => null,
        ]);
    }

    public function getVisitor(): Visitor
    {
        return $this->visitor;
    }

    public function getVisited(): Visited
    {
        return $this->visited;
    }

    public function headers(): Closure
    {
        return $this->assetFactory->asHeaders();
    }

    public function hasVisit(): bool
    {
        if (
            $this->visitor instanceof Visitor &&
            $this->visited instanceof Visited &&
            $this->assetFactory->requestPermission()
        ) {
            return true;
        }

        return false;
    }
}
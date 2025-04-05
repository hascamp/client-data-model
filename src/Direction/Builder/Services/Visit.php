<?php

namespace Hascamp\Direction\Builder\Services;

use Closure;
use Hascamp\Client\Models\User;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Builder\DataVisited;
use Hascamp\Direction\Builder\DataVisitor;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;
use Illuminate\Http\Request;

class Visit implements Visitable
{
    /** @var \Hascamp\Direction\Contracts\Visitor */
    protected $visitor;

    /** @var \Hascamp\Direction\Contracts\Visited */
    protected $visited;

    public function __construct(
        private BasePlatform $base,
        private Request $http,
        private ?User $user,
    )
    {}

    public function setVisitor(): void
    {
        $this->visitor = DataVisitor::from([
            'id' => $this->user?->id ?? null,
            'hspid' => $this->user?->hspid ?? null,
            'name' => $this->user?->name ?? null,
            'username' => $this->user?->username ?? null,
        ]);
    }

    public function setVisited(): void
    {
        $this->visited = DataVisited::from([
            'target' => $this->http?->routeAs()?->route(), // sementara dalam pengujian ...
            'targetId' => null,
            'visitAs' => null,
            'visitAsId' => $this->user?->hspid,
            'visitRoleAs' => null,
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
}
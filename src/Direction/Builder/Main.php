<?php

namespace Hascamp\Direction\Builder;

use Closure;
use Illuminate\Http\Request;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Contracts\Accessible;
use Hascamp\Direction\Builder\Services\Visit;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Contracts\Service\Requestion;
use Hascamp\Direction\Exceptions\VisitIdentification;
use Hascamp\Direction\Builder\Factory\AssetRequestFactory;
use Hascamp\Direction\Builder\Master\Stream as BuilderApp;

final class Main extends BuilderApp implements Accessible
{
    /** @var \Hascamp\Direction\Contracts\Service\Visitable */
    private $visit;
    
    /** @var \Hascamp\Direction\Builder\Factory\AssetRequestFactory */
    private $assetFactory;

    public function __construct(Requestion $requestion, array $config)
    {
        parent::__construct(
            $requestion,
            $config
        );
    }

    public function visitDirector(Request $request, ?Closure $director): void
    {
        if ($this->visit !== null) {
            throw new VisitIdentification(
                "The visitor is suspected of violating the access policy by exceeding the allowed request limit.",
                403,
                [
                    "policy" => "Only one call may be made in one http request cycle."
                ]
            );
        }

        $factory = new AssetRequestFactory;
        $this->assetFactory = $factory($this->app(), $request->routeAs()->route(), $request->user()?->hspid);

        $this->visit = new Visit(
            $this->app(),
            $request,
            $request->user(),
        );

        if ($director instanceof Closure) {
            $director($this);
        }
    }

    public function hasVisit(): bool
    {
        if (
            $this->visit?->getVisitor() instanceof Visitor &&
            $this->visit?->getVisited() instanceof Visited &&
            $this->assetFactory?->requestPermission()
        ) {
            return $this->visit?->getVisitor()?->hspid !== null || $this->visit?->getVisited()?->target !== null;
        }

        return false;
    }
    
    public function visitBuilder(): void
    {
        $this->visit->setVisitor();
        $this->visit->setVisited();

        $this->set_visit_access_permission($this->hasVisit());
        $this->optimize_request_preparation($this->assetFactory->asHeaders(), function ($app) {
            $this->app = $app; // preset...
        });
    }
    
    public function visit(): Visitable
    {
        $this->ensure_visits($this->visit);
        return $this->visit;
    }
    
    public function visitor(): Visitor
    {
        return $this->visit()->getVisitor();
    }
    
    public function visited(): Visited
    {
        return $this->visit()->getVisited();
    }
}
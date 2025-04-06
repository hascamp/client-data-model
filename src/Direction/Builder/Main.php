<?php

namespace Hascamp\Direction\Builder;

use Closure;
use Illuminate\Http\Request;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Contracts\Accessible;
use Hascamp\Direction\Builder\Services\Visit;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Exceptions\VisitIdentification;
use Hascamp\Direction\Builder\Master\Stream as BuilderApp;

final class Main extends BuilderApp implements Accessible
{
    public function __construct(string $requestion, array $config)
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

        $routeName = $request->routeAs()?->route() ?? $request->route()->action['as'];

        $this->visit = new Visit(
            $this->app(),
            $routeName,
            $request->user(),
        );

        if ($director instanceof Closure) {
            $director($this);
        }
    }
    
    public function visitBuilder(): void
    {
        $this->visit->setVisitor();
        $this->visit->setVisited();

        $this->set_visit_access_permission($this->hasVisit());
        $this->optimize_request_preparation(function ($app) {
            $this->app = $app; // preset...
        });
    }

    public function hasVisit(): bool
    {
        if (
            $this->visit?->getVisitor() instanceof Visitor &&
            $this->visit?->getVisited() instanceof Visited &&
            $this->visit?->getAssetFactory()?->requestPermission()
        ) {
            return $this->visit?->getVisitor()?->hspid !== null || $this->visit?->getVisited()?->target !== null;
        }

        return false;
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
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
use Hascamp\Direction\Builder\Master\Stream as BuilderApp;

final class Main extends BuilderApp implements Accessible
{
    /** @var \Hascamp\Direction\Contracts\Service\Visitable */
    private $visitable;

    public function __construct(Requestion $requestion, array $config)
    {
        parent::__construct(
            $requestion,
            $config
        );
    }

    public function visitDirector(Request $request, ?Closure $director): void
    {
        if ($this->visitable !== null) {
            throw new VisitIdentification(
                "Visitors are suspected of violating the access policy by exceeding the permitted request limit.",
                403,
                [
                    "policy" => "Only one call may be made in one http request cycle."
                ]
            );
        }

        $visit = new Visit(
            $this->app(),
            $request->user(),
            $request->routeAs()->route(),
        );
        
        $this->ensure_visits($visit);
        $this->visitable = $visit;

        if ($director instanceof Closure) {
            $director($this);
        }
    }
    
    public function visitBuilder(): void
    {
        $this->visitable->setVisitor();
        $this->visitable->setVisited();

        $this->set_visit_access_permission($this->visitable->hasVisit());
        $this->requestion_optimize();
    }

    protected function requestion_optimize(): void
    {
        $this->requestion->setHeader($this->visitable->headers());
    }

    public function visitPermission(): bool
    {
        return $this->isPermitted;
    }
    
    public function visit(): Visitable
    {
        $this->ensure_visits($this->visitable);
        return $this->visitable;
    }
    
    public function request(): Requestion
    {
        $this->ensure_request($this->requestion);
        return $this->requestion;
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
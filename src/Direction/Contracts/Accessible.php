<?php

namespace Hascamp\Direction\Contracts;

use Closure;
use Illuminate\Http\Request;
use Hascamp\Direction\Contracts\Visited;
use Hascamp\Direction\Contracts\Visitor;
use Hascamp\Direction\Contracts\Service\Visitable;
use Hascamp\Direction\Contracts\Service\Platform\BasePlatform;

interface Accessible
{
    public function app(): BasePlatform;

    public function visitDirector(Request $request, Closure $visitable): void;
    public function visitBuilder(): void;
    public function visit(): Visitable;
    public function visitor(): Visitor;
    public function visited(): Visited;
}
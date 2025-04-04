<?php

namespace Hascamp\Client\Resources\Call;

use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;
use Jet\Request\Client\Contracts\Requestionable;

class Ping extends DataModel implements Modelable
{
    public function __construct(
        public readonly array $connection = [],
        public readonly array $documentations = [],
    )
    {
        parent::__construct();
    }

    public static function index(): static
    {
        return static::connection(
            function($request) {
                $request
                ->method('get')
                ->url('ping');
            },
            function($response) {
                if($response instanceof Requestionable) {
                    return $response->getResults(['results'])['results'];
                }

                return [];
            }
        );
    }
}
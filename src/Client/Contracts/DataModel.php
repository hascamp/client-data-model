<?php

namespace Hascamp\Client\Contracts;

use Closure;
use Throwable;
use Jet\Request\Client;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Log;

abstract class DataModel extends Data
{
    protected array $dataForm = [];
    protected array $headers = [];

    public function __construct(
        protected bool $successful = false,
        protected int $statusCode = 500,
        protected string $message = "There was a problem with the internal server.",
    )
    {}

    public function requestion($data, $headers)
    {
        $this->dataForm = $data;
        $this->headers = $headers;
    }

    /**
     * Original Data Results
     * 
     * @var array
     */
    protected array $original_data_results = [];
    
    final protected function connection(string $method, string $url = "", ?Closure $results = null): static
    {
        $response = Client::request($this->dataForm, function ($request) use ($method, $url) {
            $request
            ->method($method)
            ->url($url)
            ->header($this->headers);
        });

        $_dataResults = function (array $results) {
            if (count($results) === 1 && isset($results['results'])) {
                return $results['results'];
            }
            else if (count($results) > 1 && isset($results['results'])) {
                return $results;
            }
        };

        if($results === null) {
            $results = function(array $r) {
                return $r;
            };
        }

        try {
            
            $_self = static::from($results($_dataResults($response->getResults('results'))));
            $_self->original_data_results = $response->getOriginalResults();
            $_self->successful = $response->getSuccessful();
            $_self->statusCode = $response->getStatusCode();
            $_self->message = $response->getMessage();
            return $_self;

        } catch (Throwable $e) {
            Log::error("Error converting Request Client object is not recognized.", [
                'MESSAGE' => $e->getMessage(),
                'CODE' => $e->getCode(),
                '__FILE__' => $e->getFile(),
                '__LINE__' => $e->getLine(),
            ]);
        }

        return static::from($results([]));
    }

    public function successful(): bool
    {
        return $this->successful;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function results(): array
    {
        return $this->all();
    }

    public function getSuccessful(): bool
    {
        return $this->successful();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode();
    }

    public function getMessage(): ?string
    {
        return $this->message();
    }

    public function getResults(): array
    {
        return $this->toArray();
    }
}
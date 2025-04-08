<?php

namespace Hascamp\Client\Contracts;

use Closure;
use Throwable;
use Jet\Request\Client;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Jet\Request\Client\Contracts\Requestionable;
use Hascamp\Client\Exceptions\DataClientResourceFailed;

abstract class DataModel extends Data
{
    protected string $requestModel = "";
    protected array $dataForm = [];
    protected array $headers = [];

    public function __construct(
        protected bool $successful = false,
        protected int $statusCode = 500,
        protected string $message = "There was a problem with the internal server.",
    )
    {}

    protected array $original_data_results = [];

    public function requestion($event, $data, $headers)
    {
        $this->requestModel = $event;
        $this->dataForm = $data;
        $this->headers = $headers;
    }
    
    final protected function connection(string $method, string $url, Closure|array|null $results = null): static
    {
        if (is_array($results)) {
            $response = $results;
        }
        else {
            $response = Client::request($this->dataForm, function ($request) use ($method, $url) {
                $request
                ->method($method)
                ->url($url)
                ->header($this->headers);
            });
        }

        if($results === null) {
            $results = function($r) {
                return $r;
            };
        }

        return $this->produceToResponse($results($response));
    }

    final protected function connectionWithProxy(string $method, string $url): static
    {
        $key = $this->requestModel ."::". (Auth::user()?->hspid ?? '0');

        $cacheable = Cache::remember($key, 3600, function () use ($method, $url) {
            $response = $this->connection($method, $url, null);
            if (! $response->successful()) {
                return null;
            }
            return $response->getOriginalResults() ?? [];
        });

        if (! $cacheable) {
            report(new DataClientResourceFailed("Connection With Proxy failed.", [
                'request_model' => $this->requestModel
            ]));
            $cacheable = [];
        }

        return $this->produceToResponse($cacheable);
    }

    protected function produceToResponse(Requestionable|array $response): static
    {
        $_dataResults = function (array $results) {
            if (isset($results['results'])) {
                return $results['results'];
            }
            else {
                return $results;
            }
        };

        try {

            if ($response instanceof Requestionable) {
                $_self = static::from($_dataResults($response->getResults('results')));
                $_self->original_data_results = $response->getOriginalResults();
                $_self->successful = $response->getSuccessful();
                $_self->statusCode = $response->getStatusCode();
                $_self->message = $response->getMessage();
            }
            else if (is_array($response)) {
                $_self = static::from($_dataResults($response));
                $_self->original_data_results = $response;
                if (isset($response['successful'])) $_self->successful = $response['successful'];
                if (isset($response['statusCode'])) $_self->statusCode = $response['statusCode'];
                if (isset($response['message'])) $_self->message = $response['message'];
            }
            else {
                $_self = $this->from([]);
            }
            
        } catch (Throwable $e) {
            report(new DataClientResourceFailed("Error converting Request Client object is not recognized."));
        }

        return $_self;
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

    public function getOriginalResults(): array
    {
        return $this->original_data_results;
    }
}
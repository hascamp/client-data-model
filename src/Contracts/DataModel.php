<?php

namespace Hascamp\Client\Contracts;

use Closure;
use Throwable;
use Jet\Request\Client;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Log;

abstract class DataModel extends Data
{
    public function __construct(
        protected bool $successful = false,
        protected int $statusCode = 500,
        protected string $message = "There was a problem with the internal server."
    )
    {
        //
    }

    /**
     * Data Form Request
     * 
     * @var array
     */
    protected static $dataForm = [];

    /**
     * Original Data Results
     * 
     * @var array
     */
    protected array $original_data_results = [];

    protected function setDataForm(array $data): void
    {
        static::$dataForm = $data;
    }

    public function getDataForm(): array
    {
        return static::$dataForm;
    }
    
    final protected static function connection(Closure $request, ?Closure $results = null): static
    {
        $response = Client::request($request);
        dd($response);

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
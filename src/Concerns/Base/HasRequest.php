<?php

namespace Hanafalah\ModuleIcd\Concerns\Base;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait HasRequest
{
    use HasConfig;

    protected array $__headers, $__queries;
    protected string $__url;
    protected object $__api_response;

    protected function http(): PendingRequest
    {
        return Http::withHeaders($this->__headers ?? [])->withQueryParameters($this->__queries ?? []);
    }

    protected function setHeader(): self
    {
        $this->__headers = [
            'Authorization'   => $this->getAuthorization(),
            'Accept'          => 'application/json',
            'Accept-Language' => $this->getConfigLang(),
            'API-Version'     => $this->getConfigApiVersion()
        ];
        return $this;
    }

    protected function post(string $end_point, array $payload): object
    {
        $response = $this->http()->asForm()->post($end_point, $payload);
        return (object) $response->json();
    }

    private function collectParameters(array $vars)
    {
        return array_reduce(array_keys($vars), function ($carry, $key) use ($vars) {
            if ($key === 'this') return $carry;

            $camelCase = Str::camel($key);
            $carry[$camelCase] = $vars[$key];
            return $carry;
        }, []);
    }

    protected function setQueries(array $vars): self
    {
        $queries = $this->collectParameters($vars);
        $this->__queries = $queries;
        return $this;
    }

    protected function setupRequest()
    {
        $response = $this->http()->get($this->__url);
        $this->__api_response = json_decode($response->body());
        return $response->status();
    }

    public function getApiResponse(): object
    {
        return $this->__api_response;
    }

    public function makeRequest()
    {
        if ($this->setupRequest() == 401) { // unauthorized token 
            $this->oauth();
            $this->makeRequest();
        }
        return $this->getApiResponse();
    }
}

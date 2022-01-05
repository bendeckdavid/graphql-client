<?php

namespace BendeckDavid\GraphqlClient\Classes;

use Illuminate\Support\Arr;
use BendeckDavid\GraphqlClient\Enums\Request;
use BendeckDavid\GraphqlClient\Classes\Mutator;

class Client extends Mutator {

    private String $query;
    public String $queryType;
    public Array $variables = [];
    public Array $rawHeaders = [
        'Content-Type' => 'application/json',
        'User-Agent' => 'Laravel GraphQL client',
    ];

    public function __construct(
        protected String|Null $endpoint
    )
    {
        //
    }


    public function getRawQueryAttribute()
    {
        $content = match($this->queryType){
            Request::RAW => $this->query,
            DEFAULT => "{$this->queryType} {{$this->query}}"
        };

        return <<<"GRAPHQL"
        {$content}
        GRAPHQL;
    }


    public function getRequestAttribute()
    {
        return stream_context_create([
            'http' => [
                'method'  => 'POST',
                'content' => json_encode(['query' => $this->raw_query, 'variables' => $this->variables]),
                'header'  => $this->headers,
            ]
        ]);
    }

    
    public function getHeadersAttribute()
    {
        $formattedHeaders = [];
        foreach ($this->rawHeaders as $key => $value) {
            $formattedHeaders[] = $key . ': ' . $value;
        }

        return $formattedHeaders;
    }

    
    public function header(String $key, String $value)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, [
            $key => $value
        ]);

        return $this;
    }

    
    public function withHeaders(Array $headers)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, $headers);

        return $this;
    }


    private function generate(String $type, String $query)
    {
        $this->queryType = $type;
        $this->query = $query;

        return $this;
    }


    public function query(string $query)
    {
        return $this->generate(Request::QUERY, $query);
    }


    public function mutation(string $query)
    {
        return $this->generate(Request::MUTATION, $query);
    }


    public function raw(string $query)
    {
        return $this->generate(Request::RAW, $query);
    }


    public function with(Array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }


    public function endpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }


    public function makeRequest()
    {
        try {
            $result = file_get_contents($this->endpoint, false, $this->request);
            $response = json_decode($result, true);
            return Arr::get($response, "data");

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function get()
    {
        return $this->makeRequest();
    }

}

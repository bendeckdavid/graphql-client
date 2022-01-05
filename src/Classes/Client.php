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


    /**
     * Generate the Graphql query in raw format
     * 
     * @return string
     */
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


    /**
     * Build the HTTP request
     * 
     * @return resource
     */
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


    /**
     * Return Client headers formatted
     * 
     * @return array
     */
    public function getHeadersAttribute()
    {
        $formattedHeaders = [];
        foreach ($this->rawHeaders as $key => $value) {
            $formattedHeaders[] = $key . ': ' . $value;
        }

        return $formattedHeaders;
    }

    
    /**
     * Allow to append a new header to the client
     * 
     * @return Client
     */
    public function header(String $key, String $value)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, [
            $key => $value
        ]);

        return $this;
    }

    
    /**
     * Allow to pass multiple headers to the client
     * 
     * @return Client
     */
    public function withHeaders(Array $headers)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, $headers);

        return $this;
    }


    /**
     * Allow to pass multiples variables to the client
     * 
     * @return Client
     */
    public function with(Array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }


    /**
     * Build a new client
     * 
     * @return Client
     */
    private function generate(String $type, String $query)
    {
        $this->queryType = $type;
        $this->query = $query;

        return $this;
    }


    /**
     * Build a new Graphql Query request
     * 
     * @return Client
     */
    public function query(string $query)
    {
        return $this->generate(Request::QUERY, $query);
    }


    /**
     * Build a new Graphql Mutation request
     * 
     * @return Client
     */
    public function mutation(string $query)
    {
        return $this->generate(Request::MUTATION, $query);
    }


    /**
     * Build a new Graphql Raw request
     * 
     * @return Client
     */
    public function raw(string $query)
    {
        return $this->generate(Request::RAW, $query);
    }


    /**
     * Allow to change an request endpoint
     * 
     * @return Client
     */
    public function endpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }


    /**
     * Execute request
     * 
     * @return array
     */
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


    /**
     * Return data
     * 
     * @return array
     */
    public function get()
    {
        return $this->makeRequest();
    }

}

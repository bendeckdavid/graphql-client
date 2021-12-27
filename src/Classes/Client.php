<?php

namespace BendeckDev\GraphqlClient\Classes;

use Illuminate\Support\Arr;
use BendeckDev\GraphqlClient\Enums\Request;
use BendeckDev\GraphqlClient\Classes\Mutator;

class Client extends Mutator {

    private String $query;
    public String $queryType;
    

    public function __construct(
        protected String $endpoint
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
                'content' => json_encode(['query' => $this->raw_query, 'variables' => []]),
                'header'  => [
                    'Content-Type: application/json', 
                    'User-Agent: Laravel GraphQL client'
                ],
            ]
        ]);                
    }


    private function generate(String $type, String $query)
    {
        $this->queryType = $type;
        $this->query = $query;

        return $this->makeRequest();
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
    
}
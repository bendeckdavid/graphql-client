<?php

namespace BendeckDavid\GraphqlClient\Classes;

use Exception;
use Illuminate\Support\Arr;
use BendeckDavid\GraphqlClient\Enums\Format;
use BendeckDavid\GraphqlClient\Enums\Request;
use BendeckDavid\GraphqlClient\Classes\Mutator;

class Client extends Mutator {

    private string $query;
    public string $queryType;
    protected string $token;
    public array $variables = [];
    public array $rawHeaders = [
        'Content-Type' => 'application/json',
        'User-Agent' => 'Laravel GraphQL client',
    ];
    public array $context = [];

    public function __construct(
        protected string|null $endpoint
    )
    {

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
        return stream_context_create(array_merge([
            'http' => [
                'method'  => 'POST',
                'content' => json_encode(['query' => $this->raw_query, 'variables' => $this->variables], JSON_NUMERIC_CHECK),
                'header'  => $this->headers,
            ]
        ], $this->context));
    }


    /**
     * Include authentication headers
     *
     * @return void
     */
    protected function includeAuthentication()
    {
        $auth_scheme = config('graphqlclient.auth_scheme');

        // Check if is a valid authentication scheme
        if (!array_key_exists($auth_scheme, config('graphqlclient.auth_schemes')))
            throw new Exception('Invalid Graphql Client Auth Scheme');

        // fill Authentication header
        $authToken = isset($this->token) ? $this->token : config('graphqlclient.auth_credentials');
        data_fill($this->rawHeaders, config('graphqlclient.auth_header'),
            config('graphqlclient.auth_schemes')[$auth_scheme].$authToken);
    }


    /**
     * Return Client headers formatted
     *
     * @return array
     */
    public function getHeadersAttribute()
    {
        // Include Authentication
        if(config('graphqlclient.auth_credentials') || isset($this->token)) {
            $this->includeAuthentication();
        }

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
    public function header(string $key, string $value)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, [
            $key => $value
        ]);

        return $this;
    }


    /**
     * Allow to append a new context info to the client
     *
     * @return Client
     */
    public function context(array $context)
    {
        $this->context = $context;
        return $this;
    }


    /**
     * Allow to pass multiple headers to the client
     *
     * @return Client
     */
    public function withHeaders(array $headers)
    {
        $this->rawHeaders = array_merge($this->rawHeaders, $headers);

        return $this;
    }


    /**
     * Allow to pass multiples variables to the client
     *
     * @return Client
     */
    public function with(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }


    /**
     * Build a new client
     *
     * @return Client
     */
    private function generate(string $type, string $query)
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
    public function makeRequest(string $format, bool $rawResponse = false)
    {
        try {
            $result = file_get_contents($this->endpoint, false, $this->request);
            if ($format == Format::JSON) {
                $response = json_decode($result, false);
                if ($rawResponse) return $response;
                return $response->data;
            } else {
                $response = json_decode($result, true);
                if ($rawResponse) return $response;
                return Arr::get($response, "data");
            }

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Return data
     * @param $format string (array|json) define return format, array by default
     *
     * @return array by default
     */
    public function get(string $format = Format::ARRAY)
    {
        return $this->makeRequest($format);
    }

    /**
     * Return raw response
     * @param $format string (array|json) define return format, array by default
     *
     * @return array by default
     */
    public function getRaw(string $format = Format::ARRAY)
    {
        return $this->makeRequest($format, true);
    }

}

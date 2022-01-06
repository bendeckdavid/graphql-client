
# Minimal GraphQL Laravel Client

Minimal GraphQL client for Laravel.


## Requirements

- Composer 2+


## Installation

Install Package (Composer 2+)
```bash
composer require bendeckdavid/graphql-client
```


## Usage

Enviroment variable 
```php
GRAPHQL_ENDPOINT="https://api.spacex.land/graphql/"
```


## Authentication

We provide a minimal authentication integration by appending the `Authorization` header to the request client. You can pass the credentials using an `env` variable.
```php
GRAPHQL_CREDENTIALS="YOUR_CREDENTIALS"
```

You can also pass auth credentials at runtime using `withToken($credentials)` method.


'Authorization' header and 'Bearer' Schema are used by default. You can override the default behaviour by defining following variables in your `.env` file.
```php
GRAPHQL_AUTHENTICATION_HEADER="Authorization"

// Allowed: basic, bearer, custom
GRAPHQL_AUTHENTICATION="bearer"
```


## Usage/Examples

Import GraphQL Client Facades
```php
use BendeckDavid\GraphqlClient\Facades\GraphQL;
```

Basic use

```php
return GraphQL::query('
    capsules {
        id
        original_launch
        status
        missions {
            name
            flight
        }
    }
')->get()
```

Mutator request:
```php
return GraphQL::mutator('
    insert_user(name: "David") {
        id
        name
        date_added
    }
')->get()
```

You can access "query" or "mutator" as a shortcut if you are not passing variables, if is not the case you must use the "raw" attribute:

```php
return GraphQL::raw('
    mutation($name: String) {
        insert_user(name: $name) {
            id
            name
            date_added
        }
    }
')
->with(["name" => "David"])
->get()
```

The `variables` or `payload` to the GraphQL request can also be passed using magic methods like:
```php
return GraphQL::raw('
    mutation($name: String) {
        insert_user(name: $name) {
            id
            name
            date_added
        }
    }
')
->withName("David")
->get()

```

If you want to address te request to another endpoint, you can do :

```php
return GraphQL::endpoint("https://api.spacex.land/graphql/")
->query('
    capsules {
        id
        original_launch
        status
        missions {
            name
            flight
        }
    }
')->get()

```


## Headers

You can include a header to the request by using the attribute "header" or add multiple headers by "withHeaders":
```php
return GraphQL::query($query)
->header('name', 'value')
->withHeaders([
    'name' => 'value',
    'name' => 'value'
])->get();
```


## Author

- David Gutierrez [@bendeckdavid](https://www.github.com/bendeckdavid)


## Top Contributors ⭐

- Ehsan Quddusi [@ehsanquddusi](https://github.com/ehsanquddusi)

## Contributors
Contributions are always welcome!



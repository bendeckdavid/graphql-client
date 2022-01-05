
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

We provide a minimal authentication integration by append the Authorization header to the request client

```php
GRAPHQL_CREDENTIALS="YOUR_CREDENTIALS"

```

'Authorization' header and 'Bearer' Schema are used by default, you can custom this by:
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

If you want to address te request to another endpoint, you can do:

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


## Author

- David Gutierrez [@bendeckdavid](https://www.github.com/bendeckdavid)


## Contributors

Contributions are always welcome!

- Ehsan Quddusi [@ehsanquddusi](https://github.com/ehsanquddusi)


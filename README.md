# Wallet API

casino api managing money related transactions

## Running the dev environment

1. Visit the instructions for creating a development workstation here: [casino/casino-workspace]
2. Access local API on [wallet.casino.test](http://wallet.casino.test)

## Migrating database

Run the migrations _(already done by `script/setup`)_
```bash
docker-compose exec wallet php artisan migrate
```

## This api follow the {json:api} specification

JSON API documents work as any other API format, you send a request to an endpoint and receive your document. 
The JSON API specification defines how resources are structured. This structure helps in normalizing how you consume the API.

For example when you make a call to a `dummy endpoint` through a simple GET request.

```
GET /dummy
Accept: application/json
```

You receive a response which should look something like this:

```
{  
   "status":200,
   "response":{  
      "data":[  
         {  
            "type":"dummy",
            "id":"1",
            "attributes":{  
               "key":"value"
            }
         }
      ]
   }
}
```

- To learn more about the specification [{json:api}](https://jsonapi.org/)

## Endpoints
 1. Documentation
    - [doc](http://wallet.casino.test/doc)
    - [swagger](http://wallet.casino.test/)
    - [swagger.json](http://wallet.casino.test/v1/swagger.json)

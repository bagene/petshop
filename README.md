## Payments Wrapper Implementation for PetShop

A simple implementation of `bagene/payments-wrapper` package with PetShop.

## Notes

- I did not include `ccv` and the whole `credit_card_number` to payment details as I think its bad to save credit card information in the database. I attached the `stripe_card_id` from stripe to view some payment details in the future.

## Installation
### Clone

- `git clone git@github.com:bagene/petshop.git`
- `cd bagene/` -> `git clone --recurse-submodules git@github.com:bagene/payments-wrapper.git` -> `composer install`
- `cd ../..` -> `composer install`
- add `\Bagene\PaymentsWrapper\PaymentServiceProvider::class,` to your `app.php` providers.
- run `php artisan vendor:publish` and select `Bagene\PaymentsWrapper\PaymentServiceProvider`

### Swagger

`npm run dev`
### env()

copy `.env.example` to `.env.testing` and `.env`

### Migrate

run `php artisan migrate --seed` to migrate database along with the seeders

### Login

- Do a request to `/api/users/login` with body of `email` and `password` to get your JWT token. 
- Attach your JWT token to your headers with header name `Authorization` as `Bearer` (`Bearer {JWT_Token}`) to access protected routes
#### Test Credentials

- email => `test@buckhill.co.uk`
- password => `userpassword`

### Test Payment

- Pay an order using endpoint `/api/orders/{uuid}/payments`.

- You can test this with a sample order provided by the seeders `/api/orders/415834b1-a411-4470-b944-5ab423fadbae/payments`

```
{
    "number": "4242424242424242",
    "exp_month": 4,
    "exp_year": 2024,
    "cvc": "314"
}
```

### Endpoints:

`/api/users/login` POST
`/api/orders/{uuid}/payments` PATCH
`/api/payments/{uuid}` GET

## Testing

`./vendor/bin/phpunit`

## Swagger Docs:

- http://petshop-payment.test
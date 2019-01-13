# Movie Ticket Booking App

# Tech stack:
##### PHP Lumen: API Server
##### MySQL: DB
##### Ionic: Frontend

# Set up server:
cd MovieTicketBookingAPI
Set up DB username, password, hostname and db name in `.env` file
Run `php composer install`
Run `php artisan migrate` to migrate database
Run command `php -S localhost:9090 -t public`
API serve on `localhost:9090`

# Set up App:
cd MovieTicketBookingApp
Run `npm install`
Run `ionic serve`
App serve on `localhost:8100`

Documentation for the API can be found [here](https://documenter.getpostman.com/view/2358344/RznHJxZo)


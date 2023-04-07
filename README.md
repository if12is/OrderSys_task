# Order_task
 This project is a simple order management system built with Laravel, which allows users to create orders by selecting products and their quantities, and then deducts the consumed ingredients from the available stock. The system also sends an email alert to the merchant when the stock of any ingredient goes below a specified threshold.


## To install Laravel


## note 
create a new database and link it in the project
Make configuration of mailgun on .env to can send mail successfully

```
------------------------------------------------------------------
composer install  
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
------------------------------------------------------------------
```
## note 
Go to app/Http/Controllers/OrderController/store function change the mail to you want to receive the alert of amount on it  

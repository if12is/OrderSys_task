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


## Api Test
if you have postman in your device follow this steps:
-------------------------------------------------------------
1- Open Postman and create a new request.
2- Set the request method to POST.
3- Enter the URL[http://localhost:8000/api/orders] of the endpoint that points to the store function of the controller.
4- In the Headers tab, add a (Content-Type) header with the value (application/json).
5- In the Body tab, select the raw option and enter a JSON payload that matches the validation rules of the function. 6- The JSON payload should include an array of products, each containing a product_id and a quantity, as well as the 7- customer_name and customer_email fields.
8- Click the Send button to send the request.
9- Here's an example of a JSON payload you could use:
```
{
    "products": [
        {
            "product_id": 1,
            "quantity": 6
        }
    ],
    "customer_name": "idjb Elsayed",
    "customer_email": "ieh@example.com"
}
```
if you don't have postman go to the documentation of postman:(Click On postman logo )
-------------------------------------------------------------

<p align="center"><a href="https://documenter.getpostman.com/view/23433467/2s93RZNqaq" target="_blank"><img src="https://res.cloudinary.com/postman/image/upload/t_team_logo/v1629869194/team/2893aede23f01bfcbd2319326bc96a6ed0524eba759745ed6d73405a3a8b67a8" width="200" alt="API Documintation"></a></p>

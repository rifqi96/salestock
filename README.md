# README #

## 1. General - How do I access the deployed site? ##
* Please use following url for api activities: https://snuggled-halyard.000webhostapp.com/api
* Please follow chapter **2. How to do API test using postman** to use the API
* Assumptions and steps:
    1.  By default, the application has 3 registered users with following credentials:
        * Admin Role: email: admin@salestock.id; password: admin
        * Customer Role: email: customer@salestock.id; password: customer
        * Customer Role: email: rifqi96@yahoo.com; password: password
    2.  Please use following Auth as header for every request:
        * ``Accept: application/json``
        * ``Authorization: Bearer *YOUR-LOGIN-API-TOKEN*``
    3.  In order to get authorization token, please use following keyword using post method:
        * ``login``
    4.  To use keywords, please use following format:
        * ``https://snuggled-halyard.000webhostapp.com/api/{keyword}``
    5.  In order to use the API, please use URL from point 1 by using format from above instruction and use following available keywords:
        * **GET METHOD**:
            * orders/ = Get All Orders
            * orders/{order_id} = Get order details
            * orders/{order_id}/status = Get order status
            * shipments/{shipment_id}/status = Get shipment status
        * **POST METHOD**:
            * register = Register
            * login = Login
            * logout = Logout
            * orders/add = Add product to an order
            * orders/coupon = Add coupon to an order
            * orders/submit = Submit an order
            * orders/{order_id}/submit/proof = Submit proof to an order
            * orders/{order_id}/ship = Ship an order
            * orders/{order_id}/cancel = Cancel an order
    6.  Please download postman collection for more about how to use the API https://drive.google.com/open?id=17PR7c3Sg3cv5MV-EHtB7dyHfumQvjctc (Detail explanation on next chapter)
	
## 2. How to do API test using postman ##
1.  Download postman collection: https://drive.google.com/open?id=17PR7c3Sg3cv5MV-EHtB7dyHfumQvjctc
2.  Import the collection to postman
3.  Run every request or do functional API test using newman

## 3. How do I run locally? ##
#### System Requirements ####

* PHP >= 7.0.0
* MariaDB / MySQL Database
* Composer (https://getcomposer.org/)
* Javascript Turned On in your browser
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

#### Prerequisite ####

####Install Composer (https://getcomposer.org/)####

###### Please Follow Steps Below in order to install the application locally
1. Git clone :
	```$ git clone https://rifqi96@bitbucket.org/rifqi96/salestock.git```
2. Dependencies Installation :
	```$ composer install```
3. Create a Database with name ```salestock```
4. Configuration:
	* Open ```.env``` and Change with your database information
5. Database Migration:
	* Run Command : ```$ php artisan migrate:refresh --seed```
	
* Now All Set Up!

#### How to run it ? ####
###### There are 2 options in order to run the application
1. Open Terminal
2. Type Command : ```$ php artisan serve```
3. Open the site through following url: ```{localhost}:8000```
	* Note: {localhost} is your localhost url. Normally *localhost* or *127.0.0.1* **
###### OR
* Open the site through following url: ```{url}/salestock/public```
	* Note: {url} is your server url.

Thank You :)
------------------

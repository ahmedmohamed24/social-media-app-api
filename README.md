# Social Media app
## Technologies:

<p align="center">
    <a href="#">
        <img src="https://img.shields.io/badge/-PHP-f5f5f5?style=for-the-badge&amp;labelColor=grey&amp;logo=PHP&amp;logoColor=white" alt="PHP" style="max-width:100%;">
    </a>
    <a href="#">
        <img src="https://img.shields.io/badge/-Docker-61dafb?style=for-the-badge&amp;labelColor=black&amp;logo=docker&amp;logoColor=61dafb" alt="docker" style="max-width:100%;">
    </a>
    <a href="#">
        <img src="https://img.shields.io/badge/-REDIS-f5f5f5?style=for-the-badge&amp;labelColor=red&amp;logo=redis&amp;logoColor=white" alt="PHP" style="max-width:100%;">
    </a>
    <a href="#">
        <img src="https://img.shields.io/badge/-Postman-F88C00?style=for-the-badge&amp;labelColor=black&amp;logo=postman&amp;logoColor=F88C00" alt="postman" style="max-width:100%;">
    </a>
    <a href="#">
        <img src="https://img.shields.io/badge/-MYSQL-f5f5f5?style=for-the-badge&amp;labelColor=09c&amp;logo=Mysql&amp;logoColor=white" alt="MYSQL" style="max-width:100%;">
    </a>
    <a href="#">
        <img src="https://img.shields.io/badge/-Firebase-white?style=for-the-badge&amp;labelColor=F88C00&amp;logo=FIREBASE&amp;logoColor=white" alt="Firebase" style="max-width:100%;">
    </a>
</p>

## Features:

* Admin
   - create admin (seeding)
   - login
   - logout
   - retrieve admin info
* Client
    - get all clients
    - create
    - update
    - delete
* User
  - register to specific client (send a welcome email and create a **Profile**)
  - login
  - logout
  - retrieve info
  - update profile info (bio,address, etc ..)
* Post
    - create (upload files with a post)(send notification to user friends, that new post created)
    - update
    - soft delete
    - paginate on posts
    - show post with related info (likes, comments, and replies)
    - restore
    - force delete
    - paginate on specific user posts
    - cache post result with redis
* Comment
    - create
    - update
    - soft delete
    - restore
    - force delete
* Reply
    - create reply to a comment
    - update
    - soft delete
    - force delete
    - restore
* Like
    - like and dislike a post
    - like and dislike a comment
    - like and dislike a reply
    - return likes and sum of them when viewing (post,likes,comments)
* Users Relations
    - add friend
    - approve friend request
    - reject a friend request
    - get friend requests list (sent and received)
    - list of friends
    - remove a friend
    - block a user
    - list of blocked users
    - unblock a user


## Getting started:
1. Fork this Repository
1. change the current directory to project path
   ex: ```media-app```
1. make the database folder ```mkdir mysql```
1. ``` docker-compose build && docker-compose up -d ```

    **alert:** </span> if there is a server running in your machine, you should stop it or change port 80 in docker-compose.yml to another port(8000)

1. install dependencies with composer ```cd src && composer install```, if you are in a production server and composer is not installed, you can install the dependencies from docker environment ``` docker-compose exec php /bin/sh``` then, ```composer install```
1. run ``` docker-compose exec php php /var/www/html/artisan migrate ```
1. run ``` docker-compose exec php php /var/www/html/artisan db:seed --class=AdminSeeder```
1. run ``` docker-compose exec php php /var/www/html/artisan test``` to run all tests and make sure everything is OK
1. import the database in POSTMAN and begin your work


**Info:** if you want only the Laravel project,
copy the  **/src** folder to wherever you want and  make database with name **store** , then generate key
1. ```php artisan key:generate```
1. ``` php artisan migrate```
1. ``` php artisan passport:install```
1. ``` php artisan db:seed --class=AdminSeeder```
1. ``` php artisan serve ```

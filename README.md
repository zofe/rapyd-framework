rapyd-framework 
===============

Project URL: [https://github.com/zofe/rapyd-framework/](https://github.com/zofe/rapyd-framework/)

Rapyd is a PHP microframework for PHP 5.3 built on top of Slim Framework, Twig, Symfony Forms, Illuminate/Database, Twitter Bootstrap.

In detail:

- Rapyd extend Slim application to give you an MVC with modular separation framework.
- It use Twig as view template engine,
- Illuminate/Database (Fluent query and schema builder, and Eloquent ORM)  as db engine 
- Symfony Forms as base of Form widgets
- Twitter bootstrap 3 as standard for html/css output.

- Then a pool of presentation widgets (DataGrids, etc..) to let you develop CRUDL application really fast.


Felice Ostuni



## take a look ##

- sandbox : http://sandbox.rapyd.com/



## install via git ##

$ git clone https://github.com/zofe/rapyd-framework.git /your/path

$ cd /your/path

$ composer install 

then remember to setup your vhost document-root to the downloaded /your/path/www

You'll get:
a simple mvc, a powerful query builder & orm, a great template engine, powerful forms:

- Slim  http://www.slimframework.com/
- Eloquent  https://github.com/illuminate/database
- Twig http://twig.sensiolabs.org/
- Symfony Forms  http://symfony.com/doc/master/book/forms.html


-- /web -- 
is the document root folder you should  set this folder as document root in your vhost

-- /src/App --  
is where to develop your application (using MVC)
for example using  eloquent-orm for you model, twig for your views, and controllers that extends \Rapyd\Controller  

-- /src/Modules --
just if you need to split application in modules 


## requirements ##

- composer http://getcomposer.org/
- PHP 5.3+  

## TO-DO ##


- reimplement rapyd CRUD widgets:
  * dataform
  * datafilter
  * dataedit


## really old version demo, documentation & source ##

Note: I'll try do reimplement this stuffs in curret version 

- http://test.rapyd.com/demo
- https://code.google.com/p/rapyd-framework/




rapyd-framework 
===============

Project URL: [https://github.com/zofe/rapyd-framework/](https://github.com/zofe/rapyd-framework/)

Rapyd is a PHP microframework for PHP 5.3 built on top of Slim Framework, Twig, Symfony Forms, Illuminate/Database, Twitter Bootstrap.

In detail:

- Rapyd extend Slim application to give you an MVC with modular separation framework
- It use Twig as view template engine
- Illuminate/Database (Fluent query and schema builder, and Eloquent ORM)  as db engine 
- Symfony Forms as base of Form widgets
- Bootstrap 3 as standard for html/css output
- A Pool of presentation widgets (DataGrids, etc..) to let you develop CRUDL applications really fast.


Felice Ostuni


## take a look ##

- sandbox : http://sandbox.rapyd.com/

## requirements ##

- composer http://getcomposer.org/
- PHP 5.3+  

## install via composer ##

```
$ composer create-project -s dev zofe/rapyd-framework rapyd-framework
```

(you can also, fork on github, download, git clone.. etc) 
Remember to setup your vhost document-root to rapyd-framework/web

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

## TO-DO ##


- reimplement rapyd CRUD widgets:
  * dataform
  * datafilter
  * dataedit


## really old version demo, documentation & source ##

Note: I'll try do reimplement this stuffs in curret version 

- http://test.rapyd.com/demo
- https://code.google.com/p/rapyd-framework/




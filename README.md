rapyd-framework 
===============

Project URL: [https://github.com/zofe/rapyd-framework/](https://github.com/zofe/rapyd-framework/)

Rapyd is a PHP5 framework made to build applications/cms/backends using the CRUD pattern. 
It has been created in 2001 and rebuilt 2012 to support H-MVC and other nice stuff.

Now it's time to learn a bit more (composer, symfony, etc..). 
I'll try to rebuild it once again, using a better approach:
namespaces, composer, some robust symfony2 component, a popular orm, twig etc.

Felice Ostuni



## take a look ##

- sandbox : http://sandbox.rapyd.com/



## install ##

$ git clone https://github.com/zofe/rapyd-framework.git /your/www/path

$ cd /your/www/path

$ composer install 


and you'll get:
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
. dataform
. datafilter
. dataedit


## old but "working" version demos, documentation & source ##

- http://test.rapyd.com/demo
- http://www.rapyd.com/page/documentation
- https://code.google.com/p/rapyd-framework/




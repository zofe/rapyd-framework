rapyd-framework 
===============

$ composer install 

and you'll get:
a simple mvc, a powerful query builder & orm, a great template engine:

- Slim  http://www.slimframework.com/
- SlimController  https://github.com/fortrabbit/slimcontroller
- Eloquent  https://github.com/illuminate/database
- Twig http://twig.sensiolabs.org/


-- /web -- 
is the document root folder

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
. datagrid
. dataform
. datafilter
. dataedit


## old but "working" version demos, documentation & source ##

- http://test.rapyd.com/demo
- http://www.rapyd.com/page/documentation
- https://code.google.com/p/rapyd-framework/



Rapyd is a PHP5 framework made to build applications/cms/backends using the CRUD pattern. 
It has been created in 2001 and rebuilt 2012 to support H-MVC and other nice stuff.

Now it's time to learn a bit more (composer, symfony, etc..). 
I'll try to rebuild it once again, using a better approach:
namespaces, composer, some robust symfony2 component, a popular orm, twig etc.

Felice Ostuni
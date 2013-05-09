<?php

namespace Rapyd;

class Application extends \Slim\Slim
{

    /**
     * @var \Illuminate\Database\Connection  instance
     */
    public $db;

    /**
     *
     * @var \Rapyd\Helpers\Url; 
     */
    public $url;

    /**
     *
     * @var array 
     */
    public $semantic = array(
        'search', 'reset', 'checkbox',
        'pag', 'orderby', 'show',
        'create', 'modify', 'delete',
        'insert', 'update', 'do_delete');

    public function __construct($config = array())
    {


        //init slim, by default with /app/Config/* arrays
        if (empty($config)) {
            $config = include __DIR__ . '/../App/Config/config.php';
            parent::__construct($config);

            $this->setupDatabase();
            $this->setupView();
            //custom call, nothing to setup
        } else {

            parent::__construct($config);
        }

        $this->url = new \Rapyd\Helpers\Url;
    }

    public function setupRoute($routes = array())
    {
        //add default routes
        if (empty($routes)) {
            $routes = include __DIR__ . '/../App/Config/routes.php';

            $module_dir = __DIR__ . '/../Modules/';
            if (file_exists($module_dir)) {
                $modules = array_diff(scandir($module_dir), array('..', '.'));
                foreach ($modules as $module) {
                    if (file_exists($module_dir . $module . '/Config/routes.php')) {
                        $module_routes = include $module_dir . $module . '/Config/routes.php';
                        $this->addRoutes($module_routes);
                    }
                }
            }
        }
        $this->addRoutes($routes);
    }

    public function setupDatabase($db = array())
    {
        //add default routes
        if (empty($db))
            $db = include __DIR__ . '/../App/Config/db.php';


        $capsule = new \Illuminate\Database\Capsule($db);
        $capsule->bootEloquent();

        // setup db
        $this->db = $capsule->connection();
    }

    public function setupView(Array $twig = array())
    {
        //add default routes
        if (empty($twig))
            $twig = include __DIR__ . '/../App/Config/twig.php';

        // Prepare view to use twig
        \Slim\Extras\Views\Twig::$twigOptions = $twig;
        $this->view(new \Slim\Extras\Views\Twig());

        //markdown
        //todo : move "twig extensions" on some config file.
        $markdown = new \dflydev\markdown\MarkdownParser();
        $markdown_extension = new \Aptoma\Twig\Extension\MarkdownExtension($markdown);
        \Slim\Extras\Views\Twig::$twigExtensions = array(
            'Twig_Extensions_Slim',
            $markdown_extension,
        );
    }

    public function addRoutes(array $routings, $condition = null)
    {
        foreach ($routings as $path => $args) {
            $httpMethod = 'any';

            // simple
            if (!is_array($args)) {
                $args = array($args);
            }

            // specific HTTP method
            if (count($args) > 1 && is_string($args[1])) {
                $classAction = array_shift($args);
                $httpMethod = array_shift($args);
                array_unshift($args, $classAction);
            }

            // readd path & extract route
            array_unshift($args, $path);
            $this->extractControllerFromRoute($args, $condition);

            // call "map" method to add routing
            $route = call_user_func_array(array($this, 'map'), $args);
            if ('any' === $httpMethod) {
                $route->via('GET', 'POST');
            } else {
                $route->via(strtoupper($httpMethod));
            }
        }
        return $this;
    }

    protected function extractControllerFromRoute(array &$args, $condition = null)
    {
        // tmp remove path
        $path = array_shift($args);

        // determine prefix (eg "\Vendor\Bundle\Controller")
        $classNamePrefix = isset($this->settings['controller.class_prefix']) ? $this->settings['controller.class_prefix'] . '\\' : '';

        // determine method suffix or default to "Action"
        $methodNameSuffix = isset($this->settings['controller.method_suffix']) ? $this->settings['controller.method_suffix'] : 'Action';
        $methodName = '';
        $className = array_shift($args);
        if (strpos($className, '\\') !== 0) {
            $className = $classNamePrefix . $className;
        }

        // having <className>:<methodName>
        if (preg_match('/^([a-zA-Z0-9\\\\_]+):([a-zA-Z0-9_]+)$/', $className, $match)) {
            $className = $match[1];
            $methodName = $match[2] . $methodNameSuffix;
        }

        // malformed
        else {
            throw new \InvalidArgumentException("Malformed class action for '$className'. Use 'className:methodName' format.");
        }

        // build & append callable
        $app = &$this;
        $callable = function() use($app, $className, $methodName, $path) {
                    $args = func_get_args();
                    $instance = new $className($app);
                    return call_user_func_array(array($instance, $methodName), $args);
                };
        if (!is_null($condition)) {
            array_push($args, $condition);
        }
        array_push($args, $callable);

        // re-add path
        array_unshift($args, $path);
        return;
    }

}
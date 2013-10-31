<?php

namespace Rapyd;

//eloquent
use \Illuminate\Database\Capsule\Manager as Capsule;
//twig
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use \Twig_SimpleFilter;
use \Twig_SimpleFunction;
use \Twig_Extension_Debug;
//symfony form & translations
use Symfony\Component\Validator\Validation;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;


// Overwrite this with your own secret
define('CSRF_SECRET', 'c2ioeEU1n48QF2WsHGWd2HmiuUUT6dxr');
define('DEFAULT_FORM_THEME', 'form_div_layout.html.twig');

define('VENDOR_DIR', realpath(__DIR__ . '/../../vendor'));
define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form/Symfony/Component/Form');
define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/validator/Symfony/Component/Validator');
define('VENDOR_TWIG_BRIDGE_DIR', VENDOR_DIR . '/symfony/twig-bridge/Symfony/Bridge/Twig');
define('VIEWS_DIR', realpath(__DIR__ . '/../App/Views'));
define('MODULES_DIR', realpath(__DIR__ . '/../Modules'));

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
     * @var \Symfony\Component\Form\FormFactory
     */
    public $form;

    /**
     *
     * @var \Slim\View;
     */
    protected $view;

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
            $this->setupForms();
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

        $capsule = new Capsule();
        $capsule->addConnection($db, 'default');
        $capsule->container['config']['database.fetch'] = \PDO::FETCH_CLASS;
        $capsule->bootEloquent();

        // setup db
        $this->db = $capsule->getConnection('default');
    }

    public function setupView(Array $twig = array())
    {
        //add default routes
        if (empty($twig))
            $twig = include __DIR__ . '/../App/Config/twig.php';

        // Prepare view to use twig
        $this->view(new \Slim\Views\Twig());
        $this->view->parserOptions = $twig;

        $views_arr = array(VIEWS_DIR, VENDOR_TWIG_BRIDGE_DIR . '/Resources/views/Form');

        $module_dir = dirname(__DIR__) . '/Modules/';
        if (file_exists($module_dir)) {
            $modules = array_diff(scandir($module_dir), array('..', '.'));
            foreach ($modules as $module) {
                if (file_exists($module_dir . $module . '/Views')) {
                    $views_arr[] = $module_dir . $module . '/Views';
                }
            }
        }
        $views_arr[] = __DIR__ . '/Views';
        $this->view->twigTemplateDirs = $views_arr;


        $this->view->parserExtensions = array(
            new Twig_Extension_Debug()
        );

        //to move somewhere
        $function = new Twig_SimpleFunction('active_class', function ($path, $class = " class=\"active\"") {
                    return (preg_match("#{$path}#", $_SERVER["REQUEST_URI"])) ? $class : '';
                }, array('is_safe' => array('html')));
        $this->view->getInstance()->addFunction($function);

        $function = new Twig_SimpleFunction('source_code', function ($filepath) {
                    $code = file_get_contents(VIEWS_DIR . $filepath);
                    $code = preg_replace("#{% block code %}.*{% endblock %}#Us", '', $code);
                    $code = highlight_string($code, TRUE);

                    return "<pre>\n" . $code . "\n</pre>";
                }, array('is_safe' => array('html')));
        $this->view->getInstance()->addFunction($function);
    }

    protected function setupForms()
    {

        // Set up the CSRF provider
        $csrfProvider = new DefaultCsrfProvider(CSRF_SECRET);

        // Set up the Translation component
        $translator = new Translator('en');
        $translator->setFallbackLocale(array('en'));

        $translator->setLocale('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource('xlf', VENDOR_FORM_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');
        $translator->addResource('xlf', VENDOR_VALIDATOR_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');

        // Set up the Validator component
        $validator = Validation::createValidatorBuilder()
                ->setTranslator($translator)
                ->setTranslationDomain('validators')
                ->getValidator();


        $formEngine = new TwigRendererEngine(array(DEFAULT_FORM_THEME));


        $twig = $this->view->getInstance();
        $formEngine->setEnvironment($twig);
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new FormExtension(new TwigRenderer($formEngine, $csrfProvider)));

        // Set up the Form component
        $this->form = Forms::createFormFactoryBuilder()
                ->addExtension(new CsrfExtension($csrfProvider))
                ->addExtension(new ValidatorExtension($validator))
                ->getFormFactory();
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

    public function urlFor($name, $params = array())
    {
        return sprintf('/%s%s', $this->view()->getLang(), parent::urlFor($name, $params));
    }

}
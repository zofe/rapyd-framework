<?php


 
class IndexTest extends \PHPUnit_Framework_TestCase
{
    public function testHello()
    {
        $_GET['name'] = 'Fabien';
 
        ob_start();
        include 'index.php';
        $content = ob_get_clean();
 
        $this->assertEquals('Hello Fabien', $content);
    }
}
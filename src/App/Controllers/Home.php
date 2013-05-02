<?php

namespace App\Controllers;

class Home extends \Rapyd\Controller
{

    public function indexAction()
    {
		 $this->render('Home', array('name' => 'Bello!'));
    }
	
	public function helloAction($name)
	{
		 $this->render('Home', array('name' => $name));
	}
	
	public function qsAction()
	{		
        $this->app->response()->write($this->app->url->append('gino',2)->append('dino',2)->get()."<br />");
        $this->app->response()->write($this->app->url->replace('key','newkey')->get()."<br />");
        $this->app->response()->write($this->app->url->value('key')."<br />");
        
    }
    
	public function datasetAction()
	{	
        $ds = new \Rapyd\Widgets\DataSet();
        $ds->source("users");
        $ds->build();   
        $this->render('dataset', array('ds' => $ds));
	}
    
	public function schemaAction()
	{	
        $schema = $this->app->db->getSchemaBuilder();
        

        $schema->dropIfExists("capocchie");
        $schema->table("capocchie", function ($table) {;
            $table->create();
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('capocchie_name');
            $table->string('capocchie_lastname');
            $table->text('abstract');
            $table->timestamps();
        });
 
        
        

	}
}
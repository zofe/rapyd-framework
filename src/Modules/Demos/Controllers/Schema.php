<?php

namespace Modules\Demos\Controllers;

class Schema extends \Rapyd\Controller
{

    public function indexAction()
    {
        $this->fillDB();
        $this->render('schema');
    }
    
    
    protected function fillDB()
    {
        //illuminate/dtabase schema builder
        $schema = $this->app->db->getSchemaBuilder();

        //tables are already there
        if ($schema->hasTable("demo_users")) return;
 
        //create all tables
        $schema->table("demo_users", function ($table) {
                    $table->create();
                    $table->increments('user_id');
                    $table->string('firstname', 100);
                    $table->string('lastname', 100);
                    $table->timestamps();
        });
        $schema->table("demo_articles", function ($table) {
                    $table->create();
                    $table->increments('article_id');
                    $table->integer('author_id')->unsigned();
                    $table->string('title', 200);
                    $table->text('body');
                    $table->boolean('public');
                    $table->timestamps();
        });
        $schema->table("demo_comments", function ($table) {
                    $table->create();
                    $table->increments('comment_id');
                    $table->integer('user_id')->unsigned();
                    $table->integer('article_id')->unsigned();
                    $table->text('comment');
                    $table->timestamps();
        });

        //populate all tables
        $users = $this->app->db->table('demo_users');
        $users->insert(array('firstname' => 'Jhon', 'lastname' => 'Doe'));
        $users->insert(array('firstname' => 'Jane', 'lastname' => 'Doe'));
        
        $articles = $this->app->db->table('demo_articles');
        for ($i=1; $i<=20; $i++){
            $articles->insert(array('title' => 'Article '.$i,
                                    'body' => 'Body of article '.$i,
                                    'public' => true,)
            );
        }

        $comments = $this->app->db->table('demo_comments');
        $comments->insert(array('user_id' => 1,
                                'article_id' => 2,
                                'comment' => 'Comment for Article 2')
        );

    }
    
    protected function drop()
    {
        $schema->dropIfExists("demo_users");
        $schema->dropIfExists("demo_articles");
        $schema->dropIfExists("demo_comments");
    }

}
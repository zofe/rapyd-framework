<?php

namespace Users\Controller;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

 
class User
{
    public function indexAction(Request $request)
    {
        $users = \Users\Model\User::all();
        return new Response($users->toJson());
    }
}
 
<?php


use Serverfireteam\Panel\libs;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 if (\Request::is('panel*'))
{
    \Config::set('auth.model', 'Serverfireteam\Panel\Admin');
    \Route::filter('auth', function()
    {                
        if (\Auth::guest()){                    
             return \Redirect::to('/panel/login')->with('message', 'Please Sign In');

        }
    });
}




Route::group(array('prefix' => 'panel' ,'before' => 'auth'), function()
{    
        // main page for the admin section (app/views/admin/dashboard.blade.php)
        Route::get('/', function()
        {
            return View::make('panelViews::dashboard');
        });
        
         Route::get('/{entity}/all', function ($entity) {
             try{
                  $controller = \App::make($entity.'Controller');
             }catch(Exception $ex){
                throw new Exception('No Controller Has Been Set for This Model ');               
             }
                                    
            if (!method_exists($controller, 'all')){                
                throw new Exception('Controller does not implement the CrudController methods!');               
            } else {
                return $controller->callAction('all', array('entity' => $entity));
            }
        });

        /*
        Route::get('/{entity}/all', function ($entity) {
            $controller = \App::make('Serverfireteam\\Panel\\'.$entity.'Controller');

            return $controller->callAction('all', array('entity' => $entity));
        });
    
         * 
         */
        
        Route::any('/{entity}/edit', function ($entity) {           
             try{
                $controller = \App::make($entity.'Controller');
            }catch(Exception $ex){    
                throw new Exception('No Controller Has Been Set for This Model!');                               
             }
            if (!method_exists($controller, 'edit')){
                throw new Exception('Controller does not implement the CrudController methods!');                                               
            } else {
                return $controller->callAction('edit', array('entity' => $entity));
            }
        });
        
        Route::post('/edit',
                array('uses' => 'Serverfireteam\Panel\ProfileController@postEdit'));                
});




 Route::post('/panel/login',function(){
 
    \Config::set('auth.model', 'Serverfireteam\Panel\Admin');

    $userdata = array(
            'email' 	=> Input::get('email'),
            'password' 	=> Input::get('password')
    );

    // attempt to do the login
    if (Auth::attempt($userdata)) {                   
        return Redirect::to('panel');
    } else {	 	
        // validation not successful, send back to form	
        return Redirect::to('panel/login')->with('message', 'Either Password or username is not correct!!');

    }
});

Route::get('/panel/password/reset/{token}', function ($token){
    return View::make('panelViews::passwordReset')->with('token', $token);
});

Route::get('/panel/logout', function (){
           
   Auth::logout(); 
    
   return Redirect::to('panel/login');
});


Route::get('/panel/changePassword', array('uses' => 'Serverfireteam\Panel\RemindersController@getChangePassword'));

Route::post('/panel/changePassword', array('uses' => 'Serverfireteam\Panel\RemindersController@postChangePassword'));

Route::post('/panel/reset', array('uses' => 'Serverfireteam\Panel\RemindersController@postReset'));

Route::get('/panel/reset', array('uses' => 'Serverfireteam\Panel\RemindersController@getReset'));

Route::get('/panel/remind',  array('uses' => 'Serverfireteam\Panel\RemindersController@getRemind'));

Route::post('/panel/remind', array('uses' => 'Serverfireteam\Panel\RemindersController@postRemind')); 

/*
Route::get('/panel', function () {
  return View::make('panelViews::dashboard');
});
 */      
Route::get('/panel/login', function () {
    if(Session::has('message')){
        $message = Session::get('message');
    }else{
        $message = 'Please Sign In';
    }
   return View::make('panelViews::login')->with('message', $message);
});


 

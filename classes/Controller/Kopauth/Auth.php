<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package   Modules
 * @category  Kopauth
 * @author    Fady Khalife
 * @link      http://opauth.org
 */
class Controller_Kopauth_Auth extends Controller
{
    /**
     * Opauth strategy entrypoint.
     * Direct to a provider for authentication or handle a callback response.
     */
    public function action_authenticate()
    {
        $kopauth = Kopauth::instance();
        $strategy = $this->request->param('strategy');
        
        // If missing strategy param or their already authenticated just return to start screen
        if (empty($strategy) OR $kopauth->is_authenticated($strategy))
        {
            $this->redirect(URL::site(Route::get('kopauth')->uri()));
        }
        
        // Run opauth
        $kopauth->run();
        
        // If it's a callback handle the response as required.
        if ($kopauth->is_callback())
        {
            $response = $kopauth->get_response();
            
            if (array_key_exists('error', $response))
            {
                // There is an error, set error flash message and direct back to the beginning
                Session::instance()->set('kopauth_error', $response['error']);
            }
            
            // Redirect to start to see authed session or error flash message
            $this->redirect(URL::site(Route::get('kopauth')->uri()));
        }
    }
    
    /**
     * Render view to display all configured providers.
     */
    public function action_providers()
    {
        if (Kohana::$environment === Kohana::PRODUCTION)
        {
            // Do not allow this view in production
            throw HTTP_Exception::factory(404,
                'The requested URL :uri was not found on this server.',
                array(':uri' => $this->request->uri())
            );
        }
        
        $this->response->body(View::factory('kopauth/providers')->render());
    }
    
    /**
     * Render view to display users session data for an authenticated provider.
     */
    public function action_sessiondata()
    {
        if (Kohana::$environment === Kohana::PRODUCTION)
        {
            // Do not allow this view in production
            throw HTTP_Exception::factory(404,
                'The requested URL :uri was not found on this server.',
                array(':uri' => $this->request->uri())
            );
        }
        
        $strategy = $this->request->param('strategy');
        
        if ( ! Kopauth::instance()->is_authenticated($strategy))
        {
            // Not authenticated for passed provide, redirect to auth
            $auth_route = URL::site(Route::get('kopauth')->uri(array(
                'action'   => 'authenticate',
                'strategy' => $strategy
            )));
            $this->redirect($auth_route);
        }
        
        // Get the session data
        $data = Kopauth::instance()->get_authenticated($strategy);
        
        // Render with data
        $this->response->body(View::factory('kopauth/sessiondata')
            ->set('data', $data)
            ->render());
    }
    
    /**
     * Destroy session data for a provider
     */
    public function action_logout()
    {
        Kopauth::instance()->clear_authenticated($this->request->param('strategy'));
        $this->redirect(URL::site(Route::get('kopauth')->uri()));
    }
}
    
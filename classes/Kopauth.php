<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package   Modules
 * @category  Kopauth
 * @author    Fady Khalife
 * @link      http://opauth.org
 */
class Kopauth extends Opauth
{
    /**
     * @var array
     */
    public $config;
    
    /**
     * @var Kohana_Session
     */
    public $session;
    
    /**
     * @var string
     */
    public $session_key = 'kopauthed';
    
    /**
     * @var array
     */
    private $_response;
    
    /**
     * @var Core_Kopauth
     */
    protected static $instance;
    
    /**
     * Construct Opauth with module config and setup class specifics.
     */
    public function __construct()
    {
        $this->config = Kohana::$config->load('kopauth')->as_array();
        $this->session = Session::instance();
        
        // Init our auth session as an array by default
        $auth_session = $this->session->get($this->session_key);
        if ( ! $auth_session)
        {
            $this->session->set($this->session_key, array());
        }

        parent::__construct($this->config, FALSE);
    }
    
    /**
     * @return Core_Kopauth
     */
    public static function instance()
    {
        if ( ! (self::$instance instanceof Kopauth))
        {
            self::$instance = new Kopauth;
        }
        return self::$instance;
    }
    
    /**
     * Assign opauth expected route params then run.
     */
    public function run()
    {
        $this->env['params'][0] = Request::$current->param('strategy');
        $this->env['params'][1] = Request::$current->param('callback');

        parent::run();
    }
    
    /**
     * Create a response from out callback data, if successful save the user response.
     */
    public function callback()
    {
        // Fetch auth response, based on transport configuration for callback
        switch($this->env['callback_transport'])
        { 
            case 'session':
                if (isset($_SESSION['opauth']))
                {
                    $this->_response = $_SESSION['opauth'];
                    unset($_SESSION['opauth']);
                }
            break;
            case 'post':
                $this->_response = unserialize(base64_decode(Request::$current->post('opauth')));
            break;
            case 'get':
                $this->_response = unserialize(base64_decode(Request::$current->query('opauth')));
            break;
            default:
                throw new Kohana_Exception('Unsupported callback_transport', NULL, Kohana_Exception::$php_errors[E_ERROR]);
            break;
        }
        
        // Process the response further if there is no error
        if (is_array($this->_response) AND ! array_key_exists('error', $this->_response))
        {
            $provider = isset($this->_response['auth']['provider'])
                ? $this->_response['auth']['provider']
                : 'Unknown';
            $timestamp = date('c');
            
            // Check we have expected response values
            if (empty($this->_response['auth']) OR 
                empty($this->_response['timestamp']) OR 
                empty($this->_response['signature']) OR 
                empty($this->_response['auth']['provider']) OR 
                empty($this->_response['auth']['uid']))
            {
                $this->_response = array(
                    'error' => array(
                        'provider' => $provider,
                        'code'     => 'incomplete_auth',
                        'message'  => 'Missing key auth response components'
                    ),
                    'timestamp' => $timestamp
                );
            }
            // Validate the response to ensure there was no tampering
            elseif ( ! $this->validate(sha1(print_r($this->_response['auth'], true)), $this->_response['timestamp'], $this->_response['signature'], $reason))
            {
                $this->_response = array(
                    'error' => array(
                        'provider' => $provider,
                        'code'     => 'invalid_auth',
                        'message'  =>  $reason
                    ),
                    'timestamp' => $timestamp
                );
            }
            // Success, save the valid user response
            else
            {
                $this->store_authenticated();
            }
        }
    }
    
    /**
     * Returns current response
     * @return  array
     */
    public function get_response()
    {
        if (empty($this->_response))
        {
            $this->_response = array(
                'error' => array(
                    'provider' => 'Unknown',
                    'code'     => 'empty_response',
                    'message'  => 'Empty Response'
                ),
                'timestamp' => date('c')
            );
        }
        
        return $this->_response;
    }
    
    /**
     * Check if the current run is a callback.
     * @return  boolean
     */
    public function is_callback()
    {
        return Arr::get($this->env['params'], 'strategy') == 'callback';
    }
    
    /**
     * Check for an authenticated provider session.
     * Checks through all  unless a $strategy_url_name is passed.
     * @param  string  $strategy_url_name
     * @return boolean 
     */
    public function is_authenticated($strategy_url_name = NULL)
    {
        $auth_session = $this->session->get($this->session_key);
        $valid_provider = isset($this->strategyMap[$strategy_url_name]['name']);
        
        if ($strategy_url_name AND $valid_provider)
        {
            return isset($auth_session[$this->strategyMap[$strategy_url_name]['name']]['uid']);
        }
        else if( ! $strategy_url_name)
        {
            foreach ($auth_session as $session)
            {
                // Check for an actual user id
                if (isset($session['uid'])) return TRUE;
            }
        }
        
        return FALSE;
    }     
     
    /**
     * Store the current authenticated response to the session.
     */
    public function store_authenticated()
    {
        $auth_session = $this->session->get($this->session_key);       
        $auth_session[$this->_response['auth']['provider']] = $this->_response['auth'];
        $this->session->set($this->session_key, $auth_session);
    }
    
    /**
     * Get stored authenticated responses from the session.
     * Returns all in an array unless a $strategy_url_name is passed.
     * @param  string $strategy_url_name
     * @param  array            If no authenticated session returns NULL
     */
    public function get_authenticated($strategy_url_name = NULL)
    {
        $auth_session = $this->session->get($this->session_key);
        $valid_provider = isset($this->strategyMap[$strategy_url_name]['name']);
        
        if ($strategy_url_name AND $valid_provider)
        {
            $authenticated = isset($auth_session[$this->strategyMap[$strategy_url_name]['name']]);
            
            if ($authenticated)
            {
                return $auth_session[$this->strategyMap[$strategy_url_name]['name']];
            }
            else
            {
                return NULL;
            }
            
        }
        else if( ! $strategy_url_name)
        {
            if ( ! empty($auth_session))
            {
                return $auth_session;
            }
        }
        
        return NULL;
    }
    
    /**
     * Clear stored authenticated session data.
     * Clears all data unless a $strategy_url_name is passed.
     * @param string $strategy_url_name
     */
    public function clear_authenticated($strategy_url_name = NULL)
    {
        $auth_session = $this->session->get($this->session_key);
        $valid_provider = isset($this->strategyMap[$strategy_url_name]['name']);
        
        if ($strategy_url_name AND $valid_provider)
        {
            unset($auth_session[$this->strategyMap[$strategy_url_name]['name']]);
        }
        else if( ! $strategy_url_name)
        {
            $auth_session = array();
        }

        $this->session->set($this->session_key, $auth_session);
    }
}

<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package   Modules
 * @category  Kopauth
 * @author    Fady Khalife
 * @link      http://opauth.org
 */
Route::set('kopauth', 'kopauth(/<action>(/<strategy>(/<callback>)))')
    ->defaults(array(
        'directory'  => 'Kopauth',
        'controller' => 'Auth',
        'action'     => 'providers'
    ));

require Kohana::find_file('vendor', 'opauth/lib/Opauth/Opauth');


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Kopauth Module Example</title>
        <link rel="stylesheet" media="screen" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.0/css/bootstrap.min.css" />
        <style>
            .container {
                max-width: none !important;
                width: 970px;
            }
        </style>
    </head>
    <body>

        <div class="container">
            
            <div class="page-header">
                <h1>Kopauth Module Example</h1>
                <h4>Authenticate with the following providers</h4>
            </div>

            <?php
                $kopauth = Kopauth::instance();
                $error_message = Session::instance()->get_once('kopauth_error');
                
                // Display error flash message if set
                if ($error_message)
                {
                    unset($error_message['raw']);
                    
                    echo '<div class="alert alert-danger">';
                    foreach ($error_message as $key => $value)
                    {
                        echo '<p><strong>'.$key.':</strong> '.$value.'</p>';
                    }
                    echo '</div>';
                }
                
                // Create list of configured providers
                foreach ($kopauth->env['Strategy'] as $strategy)
                {
                    $is_authenticated = $kopauth->is_authenticated($strategy['strategy_url_name']);
                    
                    $auth_route = URL::site(Route::get('kopauth')->uri(array(
                        'action'   => 'authenticate',
                        'strategy' => $strategy['strategy_url_name']))
                    );
                    
                    $button_class = $is_authenticated ? 'btn-success' : 'btn-primary';
                    
                    // Start Row
                    echo '<div class="row">';
                    
                    // Auth Buttom Column
                    echo '<div class="col-lg-2"><p>';
                    echo '<a href="'.$auth_route.'" class="btn '.$button_class.' btn-block">'.$strategy['strategy_name'].'</a>';
                    echo '</p></div>';
                    
                    // View Data Column
                    echo '<div class="col-lg-2"><p>';
                    if ($is_authenticated)
                    {
                        $sessiondata_route = URL::site(Route::get('kopauth')->uri(array(
                            'action'   => 'sessiondata',
                            'strategy' => $strategy['strategy_url_name']))
                        );
                        
                        echo '<a href="'.$sessiondata_route.'" class="btn btn-default btn-block">Session Data</a>';
                    }
                    echo '</p></div>';
                    
                    // Logout Column
                    echo '<div class="col-lg-2"><p>';
                    if ($is_authenticated)
                    {
                        $logout_route = URL::site(Route::get('kopauth')->uri(array(
                            'action'   => 'logout',
                            'strategy' => $strategy['strategy_url_name']))
                        );
                        
                        echo '<a href="'.$logout_route.'" class="btn btn-default btn-block">Logout</a>';
                    }
                    echo '</p></div>';
                    
                    // End Row
                    echo '</div>';
                }
            ?>
            
        </div>
    </body>
</html>

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
            
            <div class="page-header" style="border-bottom: none;">
                <h1>Kopauth Module Example</h1>
                <h4>Session data for <?php echo $data['provider']; ?></h4>
            </div>
            
            <table class="table" style="word-wrap:break-word; table-layout: fixed;">
                <tbody>
                <?php
                    // Remove and keep raw data for dump display
                    $raw = $data['raw']; unset($data['raw']);
                    
                    // Recursively traverse data as there may be nested arrays depending on provider
                    $rai = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
                    
                    foreach ($rai as $key => $value)
                    {
                        echo '<tr>';
                        
                        echo '<td width="180"><strong>';
                        echo $key;
                        echo '</strong></td>';
                        
                        echo '<td>';
                        
                        // Some simple formatting
                        if ($key == 'image')
                        {
                            echo '<img src="'.$value.'" class="img-thumbnail">';
                        }
                        elseif (filter_var($value, FILTER_VALIDATE_EMAIL))
                        {
                            echo '<a href="mailto:'.$value.'">'.$value.'</a>';
                        }
                        elseif (filter_var($value, FILTER_VALIDATE_URL))
                        {
                            echo '<a href="'.$value.'" target="_blank">'.$value.'</a>';
                        }
                        else
                        {
                            echo $value;
                        }
                        
                        echo '</td>';
                        
                        echo '</tr>';
                    }
                    
                    // Show dump of raw data
                    echo '<tr>';
                    
                    echo '<td><strong>raw</strong></td>';
                    
                    echo '<td>';
                    echo Debug::vars($raw);
                    echo '</td>';
                        
                    echo '</tr>';
                ?>
                </tbody>
            </table>
            
            <div class="page-header" style="border-bottom: none;">
                <div class="row">
                    <a href="<?php echo URL::site(Route::get('kopauth')->uri()); ?>" class="btn btn-default">Return to Providers</a>
                </div> 
            </div>
            
        </div>
    </body>
</html>

#kohana-kopauth

A [Kohana](http://kohanaframework.org/) module integrating [Opauth](http://opauth.org/) which provides a standardized method to interface with authentication providers like Facebook, Google, Twitter and many others.

## Demo

[Click here to see the Kopauth demo.](http://www.fkportfolio.com/playground/kopauth)

## Adding the module

In your `application/bootstrap.php` file modify the call to Kohana::modules and include this module.

    Kohana::modules(array(
        ...
        'kopauth'    => MODPATH.'kopauth',
        ...
    ));
    
## Adding strategies

Find and download strategies at [https://github.com/opauth/opauth#available-strategies](https://github.com/opauth/opauth#available-strategies)

Extract each into a separate folder in `MODPATH/kopauth/vendor/opauth/lib/Opauth/Strategy`.

Refer to each strategies documentation for usage and configuration information.

## Configuration

The kopauth config file is located in `MODPATH/kopauth/config/kopauth.php`.

You should copy this file to `APPPATH/config/kopauth.php` and make changes there, in keeping with the cascading filesystem.

Update the `security_salt`, Opauth will throw an error if you use the default value.

Add the required configuration for the strategies you added.

    'Strategy' => array(
        ...
        'Facebook' => array(
            'app_id'     => 'YOUR APP ID',
            'app_secret' => 'YOUR APP SECRET',
        ),
        ...
    )

For more information about available configuration keys and values visit https://github.com/opauth/opauth/wiki/Opauth-configuration

This module uses session for data storage. If you have not done so, add a cookie salt to your `application/bootstrap.php`.
    
    Cookie::$salt = 'YOUR SECRET SALT';
    
## Examples

This module comes with the [online demo](http://www.fkportfolio.com/playground/kopauth) included.

Once configured, browse to `http://yoursite.com/kopauth` to access it. Note that this is disabled if you Kohana::$environment === Kohana::PRODUCTION.

The Kopauth class has additional methods to assist with authentication integration. View the example controller and views to help you get started.
    
## Compatibility

This module is compatible with Kohana 3.3

Included [Opauth](http://opauth.org/) version is 0.4.4

While in theory all strategies should work, only the following have been tested.

* [Disqus](https://github.com/rasa/opauth-disqus/tree/37d80669d6d932e346f95fc3b0340aba690486b3)
* [Facebook](https://github.com/opauth/facebook/tree/28c0e53393a03a66cbfea03073d1d6aacfaddb69)
* [Github](https://github.com/opauth/github/tree/9c4fe16dc6498b2c94f4c2a41ab93b0fe4b7fa73)
* [Google](https://github.com/opauth/google/tree/35df77684c14acb346a8c3753ae3809852d1a47e)
* [Instagram](https://github.com/muhdazrain/opauth-instagram/tree/6d72ae8f27f26a666562a963296937c62d43f5cb)
* [Linkedin](https://github.com/opauth/linkedin/tree/76df2b9520b02f4e87d1d0bd6ce64a375dcba03c)
* [(Windows) Live](https://github.com/opauth/live/tree/2a854d68cd5fbf10013afbf6008fff849f1f2d0b)
* [Twitter](https://github.com/opauth/twitter/tree/24792d512ccc67e7d11e9249737616f039551c11)
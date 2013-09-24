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

Once configured, browse to `http://yoursite.com/kopauth` to access it. Note that this is disabled if your Kohana::$environment === Kohana::PRODUCTION.

The Kopauth class has additional methods to assist with authentication integration. View the example controller and views to help you get started.
    
## Compatibility

This module is compatible with Kohana 3.3

Included [Opauth](http://opauth.org/) version is 0.4.4

While in theory all strategies should work, only the following have been tested.

* [Disqus](https://github.com/rasa/opauth-disqus)
* [Facebook](https://github.com/opauth/facebook)
* [Flickr](https://github.com/pocket7878/opauth-flickr)
* [Github](https://github.com/opauth/github)
* [Google](https://github.com/opauth/google)
* [Instagram](https://github.com/muhdazrain/opauth-instagram)
* [Linkedin](https://github.com/opauth/linkedin)
* [(Windows) Live](https://github.com/opauth/live)
* [Twitter](https://github.com/opauth/twitter)
* [Linkedin](https://github.com/opauth/linkedin)

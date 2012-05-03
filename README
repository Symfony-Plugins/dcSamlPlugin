# dcSamlPlugin #

## Introduction ##
This plugin provides SSO authentication and authorization for symfony 
applications based in [SAML].

SSO provided by Saml allow developers to concentrate in business logic 
delegating all authentication and authorization work to Saml Identity 
Manager.

The plugin installation is as simply as described here.

An other additional feature is that once a user is authenticated 
in one of the applications using Saml, you will be automatically 
authenticated in the others applications

## Subversion download ##
You can download the latest version of this plugin from:

     http://svn.symfony-project.com/plugins/dcSamlPlugin/trunk/

## Installation

  * svn installation:

        [bash]
        $ svn co http://svn.symfony-project.com/plugins/dcSamlPlugin/trunk/ plugins/dcSamlPlugin

  * install as a plugin:
  
        [bash]
        $ ./symfony pl:i dcSamlPlugin

  * Enable the plugin in your proyect configuration (only if installed through svn)

        [php]
        // in config/ProjectConfiguration.class.php add:
        $this->enablePlugin("dcSamlPlugin");

  * clear the cache

        [bash]
        $ ./symfony cc


## Configuration ##

* In your app.yml add the following configuration lines
    * Is important that you know the Login URL of Saml
    * Is important that you know the Logout URL of Saml
    * Saml server x509 Certificate

Example

      all:
        .....
        dc_saml_plugin:
        # Saml Server settings
          login_url: http://localhost/simplesaml/saml2/idp/SSOService.php    
          logout_url: http://localhost/simplesaml/saml2/idp/initSLO.php?RelayState=
          certificate: <?php echo file_get_contents(sfConfig::get('sf_root_dir').'/saml.cert');?> # if you have a file with the saml certificate called saml.cert
          
          name_identifier_format: "urn:oasis:names:tc:SAML:2.0:nameid-format:persistant"
          application_issuer: application-identification-name

          # The prefix to delete from the appliction credentials.
          # If you have this credentials for your application:
             # [application-identification-name.delete_something, application-identification-name.create_something]
             # the prefix should be "application-identification-name"
          remove_permission_prefix: prefix

        # Where do you want the plugin redirects you when login or logout
          success_signin_url: @homepage
          success_signout_url: @homepage

        # This module actions are if you want to redefine them. Do not recomended 
          security_check_module: dcSamlAuth
          security_check_action: securityCheck

        # In this case, the permission attributes are like
        # array("permissions" => array("permission_name" => "prefix.permission"))
          attribute_name_of_the_credential_name: permission_name
          credentials_attribute_name: permissions



  
* In your settings.yml enable crJossoAuth module and change 

Example

      enabled_modules:       [default, dcSamlAuth, .... ]
      login_module:          dcSamlAuth
      login_action:          signin



* Prepend the following routing rules in routing.yml:

Example

      dc_saml_signin:
        url:   /login
        param: { module: dcSamlAuth, action: signin }

      dc_saml_signout:
        url:   /logout
        param: { module: dcSamlAuth, action: signout }

      ....


* Change the security filter: filters.yml

Example

     rendering: ~
     security:
       class: dcSamlSecurityFilter
     
     ...



* Change the parent class of myUser.class.php:

Example

    class myUser extends dcSamlSecurityUser
    {
    }



* Remember that it is important to change the session_name in factories.yml

Example

      all:
        storage:
          class: sfSessionStorage
          param:
            session_name: saml-test


##Author##
Matías E. Brown <mbrown@cespi.unlp.edu.ar>

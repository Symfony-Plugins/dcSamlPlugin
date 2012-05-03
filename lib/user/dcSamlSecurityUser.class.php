<?php

/**
 * PHP Saml Security User implementation for symfony
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */

class dcSamlSecurityUser extends sfBasicSecurityUser {
  

  /**
  * Implements the actionless of user signin, populating current object
  * with important data of SamlUser
  *
  * @access public
  *
  */
  public function signIn()
  {
    $this->setAuthenticated(true);
    $this->loadSamlCredentials();
  }

  /**
  * Returns a SamlUser Object
  *
  * @return SamlUser or null if it can't retrieve it.
  *
  * @access public
  */
  public function getSamlUser()
  {
    $saml_agent = SamlAgent::getNewInstance();
    try
    {
      return $saml_agent->getUserInSession();
    }
    catch(Exception $e)
    {
      return null;
    }
  }

  /**
  * Implements the actionless of user signout, cleaning the context
  *
  * @access public
  */
  public function signOut()
  {
    $this->setAuthenticated(false);
    $saml_agent = SamlAgent::getNewInstance();
    $saml_agent->deleteSession();
  }

  /**
  * Gives current symfony user all credentials taken from 
  * Saml User
  *
  * @access protected
  */
  protected function loadSamlCredentials()
  {
    $saml_user = $this->getSamlUser();
    if (is_null($saml_user))
      return;
    foreach($saml_user->getPermissions() as $permission)
    {
      $remove = sfConfig::get('app_dc_saml_plugin_remove_permission_prefix', '');
      $perm_name = $permission->getAttribute(sfConfig::get('app_dc_saml_plugin_attribute_name_of_the_credential_name', 'permission_name'));
      if($remove == '')
      {
        if(strpos($perm_name, $remove) === 0)
        {
          $permission_name = str_replace(!empty($remove)?$remove.'.':'', '', $perm_name);
          $this->addCredential($permission_name);
        }
      }
      else
      {
        $this->addCredential($perm_name);
      }
    }
  }
}

<?php
/**
 * PHP Saml Symfony Security Filter 
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */


class dcSamlSecurityFilter extends sfBasicSecurityFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    /* Disable security on josso security checks */
    if (
      ((sfConfig::get('app_dc_saml_plugin_security_check_module', 'dcSamlAuth') == $this->context->getModuleName()) && 
      (sfConfig::get('app_dc_saml_plugin_security_check_action', 'securityCheck') == $this->context->getActionName()))
            ||
      (('dcSamlAuth' == $this->context->getModuleName()) && 
      ('signin' == $this->context->getActionName()))
    )
    {
      $filterChain->execute();
      return;
    }
    else
    {
      //Check if user authenticated 
      if (!$this->context->getUser()->isAuthenticated())
      {
        // Then we need to relogin on Saml Server
        $this->forwardToLoginAction();
      }
    }
    parent::execute($filterChain);
  }
}

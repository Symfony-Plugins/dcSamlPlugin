<?php
/**
 * Real implementation of dcSamlAuth actions
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */

class BasedcSamlAuthActions extends sfActions
{
  /**
  * Signin action
  * This action in any other application must be responsable of showing
  * the login form and authentication user.
  * In this case every job is delegated to Saml
  *
  * @access public
  *
  */
  public function executeSignin(sfWebRequest $request)
  {
    $agent = SamlAgent::getNewInstance();
    if (!is_null($agent->getSession())){
      $this->getUser()->signIn($agent->getSession());
      $original_url = $this->getUser()->getAttribute('SAML_ORIGINAL_URL');
      if (!is_null($original_url)) $this->redirect($original_url);
      $this->redirect('@homepage');
    }else{
      $this->forwardToLoginAction();
    }
  }
  
  /**
  * Signout action
  * This action in any other application must be responsable of cleainig
  * session objects
  * In this case every job is delegated to JOSSO and same cleaning is done
  *
  * @access public
  *
  */
  public function executeSignout(sfWebRequest $request)
  {
    $this->loadHelpers();
    $saml_agent = SamlAgent::getNewInstance();
    $sign_out_url = $saml_agent->getSuccessSignoutUrl();
    $sign_out_url = !empty($sign_out_url)?$sign_out_url:'@homepage';
    $logoutUrl = $saml_agent->getLogoutUrl().url_for($sign_out_url,true);
    $this->getUser()->signOut();
    $this->redirect($logoutUrl);
  }

  /**
  * Check if
  *
  * @access public
  *
  */
  public function executeSecurityCheck(sfWebRequest $request)
  {
    $saml_agent = SamlAgent::getNewInstance();
    if($saml_agent->isValidResponse($request)){
      $sign_in_url = $saml_agent->getSuccessSigninUrl();
      $sign_in_url = !empty($sign_in_url)?$sign_in_url:'@homepage';
      $saml_agent->setSession($request);
      $this->getUser()->signIn();
      $this->redirect($sign_in_url);
    }
    else{
      $this->getUser()->setFlash('error', 'Invalid SAML response.');
      $this->redirect('@dc_saml_signin');
    }
  }

  /**
  * It builds login URL and redirects the request
  *
  * @access private
  *
  */
  private function forwardToLoginAction()
  {
    $this->loadHelpers();
    $uri = $this->getRequest()->getUri();
    $this->getUser()->setAttribute('SAML_ORIGINAL_URL',$uri);
    $agent = SamlAgent::getNewInstance();
    $this->redirect($agent->createRequest());
  }
  
  /**
   * Load the needed helpers 
   */
  private function loadHelpers()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
  }

}

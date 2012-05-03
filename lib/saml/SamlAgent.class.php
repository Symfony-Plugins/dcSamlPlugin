<?php

/**
 * PHP Saml Agent implementation
 *
 * @package lib.saml
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */

class SamlAgent  {

  // ---------------------------------------
  // Saml Agent configuration : 
  // --------------------------------------- 

  /**
    * @var string
    * @access private
    */
  private $idp_sso_target_url;
  
  /**
    * @var string
    * @access private
    */
  private $logout_url;

  /**
    * @var string
    * @access private
    */
  private $certificate;

  /**
    * @var string
    * @access private
    */
  private $issuer;

  /**
    * @var string
    * @access private
    */
  private $name_identifier_format;
  
  /**
    * @var string
    * @access private
    */
  private $success_signin_url;
  
  /**
    * @var string
    * @access private
    */
  private $success_signout_url;


  /**
    * @return SamlAgent a new Saml PHP Agent instance.
    */
  public static function getNewInstance() {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    // Get config variable values from app.yml.
    $saml_idp_sso_target_url = sfConfig::get('app_dc_saml_plugin_login_url');
    $saml_logout_url = sfConfig::get('app_dc_saml_plugin_logout_url');
    $saml_certificate = file_get_contents(sfConfig::get('app_dc_saml_plugin_certificate_file', sfConfig::get('sf_root_dir').'/saml.cer'));
    $saml_issuer = sfConfig::get('app_dc_saml_plugin_application_issuer');
    $saml_name_identifier_format = sfConfig::get('app_dc_saml_plugin_name_identifier_format');
    $saml_success_signin_url = url_for(sfConfig::get('app_dc_saml_plugin_success_signin_url'), true);
    $saml_success_signout_url = url_for(sfConfig::get('app_dc_saml_plugin_success_signout_url'), true);
    return new SamlAgent($saml_idp_sso_target_url,
                          $saml_logout_url,
                          $saml_certificate,
                          $saml_issuer,
                          $saml_name_identifier_format,
                          $saml_success_signin_url,
                          $saml_success_signout_url
        );
  }

  /**
  * constructor
  *
  * @access private
  *
  * @param    string $saml_idp_sso_target_url
  * @param    string $saml_certificate
  * @param    string $saml_issuer
  * @param    string $saml_name_identifier_format
  * @param    string $saml_logout_url
  * @param    string $saml_success_signin_url
  * @param    string $saml_success_signin_url
  */
  private function __construct($saml_idp_sso_target_url, $saml_logout_url, $saml_certificate, $saml_issuer, $saml_name_identifier_format, $saml_success_signin_url, $saml_success_signout_url) {
    // Agent config
    $this->idp_sso_target_url = $saml_idp_sso_target_url;
    $this->saml_logout_url = $saml_logout_url;
    $this->certificate = $saml_certificate;
    $this->issuer = $saml_issuer;
    $this->name_identifier_format = $saml_name_identifier_format;
    $this->logout_url = $saml_logout_url;
    $this->success_signin_url = $saml_success_signin_url;
    $this->success_signout_url = $saml_success_signout_url;
  }

  /**
    * Returns the Saml idp URL used to authenticate the user.
    *
    * @return string the configured Idp Sso url.
    *
    * @access public
    */
  public function getLoginUrl() {
      return $this->idp_sso_target_url;
  }

  /**
    * Returns the Saml idp URL used to deauthenticate the user.
    *
    * @return string the configured logout Idp Sso url.
    *
    * @access public
    */
  public function getLogoutUrl() {
      return $this->logout_url;
  }

  /**
    * Returns the x509 Cerificate for the users account in the IdP.
    *
    * @return string the certificate.
    *
    * @access public
    */
  public function getCertificate() {
      return $this->certificate;
  }

  /**
    * Returns the idp identifier format.
    *
    * @return string the name identifier format.
    *
    * @access public
    */
  public function getNameIdentifierFormat() {
      return $this->name_identifier_format;
  }

  /**
    * Returns the name of the application issuer.
    *
    * @return string the issuer.
    *
    * @access public
    */
  public function getIssuer() {
      return $this->issuer;
  }
  
  /**
   * 
   * @return string the success signin url.
   *
   * @access public
   */
  public function getSuccessSigninUrl() {
      return $this->success_signin_url;
  }
  
  /**
   * 
   * @return string the success signin url.
   *
   * @access public
   */
  public function getSuccessSignoutUrl() {
      return $this->success_signout_url;
  }
  
  /**
   * Return the saml setting for the connection
   * 
   * @return SamlSetting
   *
   * @access private
   */
  private function getSamlSettings()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    
    $settings = new SamlSettings();

    // When using Service Provider Initiated SSO (starting at index.php), this URL asks the IdP to authenticate the user.
    $settings->idp_sso_target_url             = $this->getLoginUrl();

    // The certificate for the users account in the IdP
    $settings->x509certificate                = $this->getCertificate();

    // The URL where to the SAML Response/SAML Assertion will be posted
    $settings->assertion_consumer_service_url = url_for('dcSamlAuth/securityCheck', true);

    // Name of this application
    $settings->issuer                         = $this->getIssuer();

    // Tells the IdP to return the email address of the current user
    $settings->name_identifier_format         = $this->getNameIdentifierFormat();
    
    return $settings;
  }
  
  /**
   * Create the saml auth server url
   * 
   * @return string the url for the saml auth server
   *
   * @access public
   */
  public function createRequest()
  {
    $authrequest = new SamlAuthRequest($this->getSamlSettings());
    return $authrequest->create();
  }
  
  /**
   * Returns the Saml response
   * 
   * @param sfWebRequest $request 
   * @return CustomSamlResponse
   *
   * @access public
   */
  public function getResponse(sfWebRequest $request)
  {
    return new CustomSamlResponse($this->getSamlSettings(), $request->getParameter('SAMLResponse'));
  }
  
  /**
   * Return if the saml response is a valid response
   * 
   * @param sfWebRequest $request
   * @return boolean
   *
   * @access public
   */
  public function isValidResponse(sfWebRequest $request)
  {
    return $this->getResponse($request)->is_valid();
  }
  
  /**
   * Return the context user
   * 
   * @return sfSecurityUser
   *
   * @access private
   */
  private function getUser()
  {
    return sfContext::getInstance()->getUser();
  }
  
  /**
   * Return the actual saml session
   * 
   * @return CustomSamlResponse
   *
   * @access public
   */
  public function getSession()
  {
    return $this->getUser()->getAttribute('SAML_SESSION_RESPONSE', null);
  }
  
  /**
   * Set the Saml response in the session
   * @param sfWebRequest $request 
   */
  public function setSession(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('SAML_SESSION_RESPONSE', $this->getResponse($request));
  }
  
  /**
   * Delete the actual saml response session 
   */
  public function deleteSession()
  {
    $this->getUser()->getAttributeHolder()->remove('SAML_SESSION_RESPONSE');
  }
  
  /**
   * Return the user in saml response session
   * 
   * @return SamlUser 
   */
  public function getUserInSession()
  {
    if($this->getUser()->getAttribute('SAML_SESSION_RESPONSE', false))
    {
      return $this->newUser($this->getUser()->getAttribute('SAML_SESSION_RESPONSE'));
    }
    return null;
  }
  
  /**
    * Factory method to build a user from saml data.
    *
    * @param CustomSamlResponse as received from server.
    * @return samluser a new samluser instance.
    *
    * @access private
    */
  private function newUser($response) {
    // Build a new samluser 
    $response->get_attributes();
    $permissions = $response->get_attribute(sfConfig::get('app_dc_saml_plugin_credentials_attribute_name', 'permissions'));
    $permissions = $permissions?json_decode($permissions):array();
    $user = new SamlUser($response->get_attributes(), $permissions);
    return $user;
  }
}
?>

<?php

/**
 * PHP Custom Saml Response implementation.
 *
 * @package lib.saml
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */
class CustomSamlResponse extends SamlResponse
{

  /**
   * Return the Dom xml xpath
   * @return DOMXPath 
   */
  private function get_xpath()
  {
    $this->xml->load(self::XML_DIR);
    $xpath = new DOMXPath($this->xml);
    $xpath->registerNamespace("samlp","urn:oasis:names:tc:SAML:2.0:protocol");
    $xpath->registerNamespace("saml","urn:oasis:names:tc:SAML:2.0:assertion");
    $xpath->registerNamespace("ds", "http://www.w3.org/2000/09/xmldsig#");
    return $xpath;
  }

  /**
    * Get the NameID provided by the SAML response from the IdP.
    */
  function get_nameid() {
    $xpath = $this->get_xpath();

    $signatureQuery = "//ds:Reference[@URI]";
    $id = substr($xpath->query($signatureQuery)->item(0)->getAttribute('URI'), 1);

    $nameQuery = "/samlp:Response/saml:Assertion[@ID='$id']/saml:Subject/saml:NameID";
    $entries = $xpath->query($nameQuery);

    return $entries->item(0)->nodeValue;
  }

  /**
   * Return the attribute value in the xaml file
   * 
   * @param string $attr
   * @return string 
   */
  public function get_attribute($attr)
  {
    $xpath = $this->get_xpath();
    $signatureQuery = "//ds:Reference[@URI]";
    $id = substr($xpath->query($signatureQuery)->item(0)->getAttribute('URI'), 1);
    $q = "//saml:Attribute[@Name='$attr']/saml:AttributeValue";
    $es = $xpath->query($q);
    return $es->item(0)->nodeValue;
  }
  
  private function get_attributes_names()
  {
    $xpath = $this->get_xpath();
    $signatureQuery = "//saml:Attribute[@Name]";
    $list = $xpath->query($signatureQuery);
    $attributes_names = array();
    for($i = 0; $i < $list->length; $i++)
    {
      $attributes_names[] = $list->item($i)->getAttribute('Name');
    }
    return $attributes_names;
  }
  
  public function get_attributes()
  {
    $xpath = $this->get_xpath();
    $signatureQuery = "//ds:Reference[@URI]";
    $id = substr($xpath->query($signatureQuery)->item(0)->getAttribute('URI'), 1);
    $attributes = array();
    foreach($this->get_attributes_names() as $attr_name)
    {
      $q = "//saml:Attribute[@Name='$attr_name']/saml:AttributeValue";
      $es = $xpath->query($q);
      $attributes[$attr_name] = $es->item(0)->nodeValue;
    }
    return $attributes;
  }
}

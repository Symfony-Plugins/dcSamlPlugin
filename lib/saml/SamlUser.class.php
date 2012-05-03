<?php

/**
 * PHP Saml User implementation.
 *
 * @package lib.saml
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */

class SamlUser {

  // User attributes
  var $attributes;
  
  // User permissions
  var $permissions;

  /**
   * Constructor
   * 
   * @param array attributes
   *
   * @access public
   */
  public function __construct($attributes, $permissions) {
    $this->attributes = $attributes;
    $this->permissions = $this->parsePermissions($permissions);
  }

  /**
    * Gets the user permissions.
    *
    * @return UserPermission[] .
    *
    * @access public
    */
  public function getPermissions()
  {
    return $this->permissions;
  }
    
  /**
   * Returns the parsed permissions
   * @param SamlPermission[] $permissions 
   */
  private function parsePermissions($permissions)
  {
    $perms = array();
    foreach($permissions as $permission)
    {
      $perms[] = new SamlPermission(get_object_vars($permission));
    }
    return $perms;
  }
  
  public function getAttribute($attribute)
  {
    if (!method_exists($this, $method = sprintf('get%s', self::camelize($attribute))))
    {
      return null;
    }
    return $this->$method();
  }
  
  public function __call($method_name, $arguments)
  {
    // guess if method type is "set" or "get"
    $method_type = substr($method_name, 0, 3);
    // guess the attribute name, CamelCased (and ucfirst)
    $attribute_name = substr($method_name, 3);
    $attribute_name = sfInflector::underscore($attribute_name);

    if ($method_type == "get")
    {
      if (isset($this->attributes[$attribute_name]))
      {
        return $this->attributes[$attribute_name];
      }
    }
    else if ($method_type == "set")
    {
      $this->attributes[$attribute_name] = $arguments[0];
    }
  }
}
?>

<?php

/**
 * PHP Saml Permission implementation.
 *
 * @package lib.saml
 *
 * @author Matías Brown <mbrown@cespi.unlp.edu.ar>
 */

class SamlPermission {
	
    // Permission's attributes array
	var $permission;
	
	/**
	 * Constructor
	 *
	 * @param array permission's attributes array
	 *
	 * @access public
	 */
	public function __construct($permission) {
		$this->permission = $permission;
	}
	
	/**
	 * Get the permission's attributes array
	 *
	 * @return array permission's attributes array.
	 * @access public
	 */
	public function getPermission()
    {
      return $this->permission;
    }
	
	/**
	 * Get perission attribute
	 *
     * @param string permission's attribute name.
	 * @return mixed the permission attribute.
	 * @access public
	 */
    public function getAttribute($attribute_name)
    {
      return $this->permission[$attribute_name];
    }
}
?>

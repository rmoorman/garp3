<?php
/**
 * Garp_Model_Validator_Abstract
 * Blueprint for Garp_Entity validators
 * @author Harmen Janssen | grrr.nl
 * @modifiedby $LastChangedBy: $
 * @version $Revision: $
 * @package Garp
 * @subpackage Validator
 * @lastmodified $Date: $
 */
abstract class Garp_Model_Validator_Abstract extends Garp_Model_Helper {
	/**
	 * Validate wether the given columns are not empty
	 * @param Array $data The data to validate
	 * @param Boolean $onlyIfAvailable Wether to skip validation on fields that are not in the array
	 * @return Void
	 * @throws Garp_Model_Validator_Exception
	 */
	abstract public function validate(array $data, $onlyIfAvailable = false);
	

	/**
	 * BeforeInsert callback.
	 * @param Array $args The new data is in $args[1]
	 * @return Void
	 */
	public function beforeInsert($args) {
		$this->validate($args[1]);
	}
	
	
	/**
	 * BeforeUpdate callback.
	 * @param Array $args The new data is in $args[1]
	 * @return Void
	 */
	public function beforeUpdate($args) {
		$this->validate($args[1], true);
	}
}
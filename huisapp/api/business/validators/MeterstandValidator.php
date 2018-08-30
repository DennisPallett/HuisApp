<?php
namespace business\validators;

class MeterstandValidator {
		
	public function isValid(\business\model\Meterstand $meterstand) 
	{
		if (empty($meterstand->opnameDatum))
			return false;

		if (empty($meterstand->water))
			return false;

		if (empty($meterstand->gas))
			return false;

		if (empty($meterstand->elektraE1))
			return false;

		return true;
	}

}
<?php
namespace business\model;

class Meterstand {
	public $opnameDatum;

	public $water;

	public $gas;

	public $elektraE1;

	public $elektraE2;

	public function setProperties ($data) {
		$this->setProperty($data, 'opnameDatum');
		$this->setIntegerProperty($data, 'water');
		$this->setIntegerProperty($data, 'gas');
		$this->setIntegerProperty($data, 'elektraE1');
		$this->setIntegerProperty($data, 'elektraE2');
	}

	private function setProperty ($data, $property) {
		if (isset($data[$property]))
			$this->$property = $data[$property];
	}

	private function setIntegerProperty ($data, $property) {
		if (isset($data[$property]) && is_numeric($data[$property]))
			$this->$property = $data[$property];
	}
}
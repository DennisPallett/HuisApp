<?php
namespace datalayer;

interface IMeterstandenDataLayer {

	function insertMeterstand(\business\model\Meterstand $meterstand);

	function getMeterstanden($sortBy, $sortOrder);
}
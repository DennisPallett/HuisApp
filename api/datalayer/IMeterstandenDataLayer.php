<?php
namespace datalayer;

interface IMeterstandenDataLayer {
	function insertMeterstand(\business\model\Meterstand $meterstand);

	function getMeterstanden($sortBy, $sortOrder);

	function deleteMeterstand($opnameDatum);

	function getMeterstand($opnameDatum);

	function updateMeterstand($opnameDatum, \business\model\Meterstand $meterstand);
}
<?php
namespace datalayer;

interface IVerbruikDataLayer {
	function getPerMaand();

	function clearVerbruik ();

	function insertVerbruik(\business\model\Verbruik $verbruik);
}
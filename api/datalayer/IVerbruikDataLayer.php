<?php
namespace datalayer;

interface IVerbruikDataLayer {
	function getPerMaand();

	function getPerJaar();

	function clearVerbruik ();

	function insertVerbruik(\business\model\Verbruik $verbruik);
}
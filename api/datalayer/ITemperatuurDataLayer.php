<?php
namespace datalayer;

interface ITemperatuurDataLayer extends \IImportDataLayer {
	function getPerMaand();
	function getPerDag($year, $month);
	function getPerUur($year, $month);
}
<?php
namespace datalayer;

interface ITemperatuurDataLayer extends \IImportDataLayer {
	function getPerMaand();
	function getPerDag();
}
<?php
namespace datalayer;

interface IDataLayer {

	function getMeterstandenData() : IMeterstandenDataLayer;

	function getVerbruikData() : IVerbruikDataLayer;

	function getImportData() : \IImportDataLayer;

}
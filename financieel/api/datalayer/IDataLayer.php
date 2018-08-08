<?php

interface IDataLayer {
	
	function getMonths();

	function getCategories();

	function getCategoriesAndGroups();

	function getReportingData() : IReportingDataLayer;

	function getStatementsData() : IStatementsDataLayer;

	function getImportData() : IImportDataLayer;


}
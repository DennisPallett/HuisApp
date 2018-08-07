<?php

interface IStatementsDataLayer {
	function deleteStatements($month, $year);

	function deleteTransactions($month, $year);

	function getStatements($year, $month, $sortBy, $sortOrder);
}
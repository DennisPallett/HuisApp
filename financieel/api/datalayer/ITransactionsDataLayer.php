<?php

interface ITransactionsDataLayer {
	function updateCategory($transactionId, $category);

	function getTransactions($year, $month, $sortBy, $sortOrder);
}
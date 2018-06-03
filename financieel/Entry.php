<?php

class Entry {
	public $id;

	public $bookingDate;

	public $valueDate;

	public $amount;

	public $description;

	public function  __construct($entryXml)
	{
		$this->id = (string) $entryXml->AcctSvcrRef;
		$this->bookingDate = (string) $entryXml->BookgDt->Dt;
		$this->valueDate = (string) $entryXml->ValDt->Dt;
		$this->amount = (double) $entryXml->Amt;

		if ($entryXml->CdtDbtInd == 'DBIT')
		{
			$this->amount = $this->amount * -1;
		}

		$this->description = (string) $entryXml->AddtlNtryInf;

		//print_r($this);
		//die();
	}
}
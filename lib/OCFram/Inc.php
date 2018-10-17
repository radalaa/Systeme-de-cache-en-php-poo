<?php
namespace OCFram;

class Inc{
	public $timestamp;
	public $datas;
	public function __construct($timestamp,$datas)
	{
		$this->timestamp = $timestamp;
		$this->datas = $datas;
		

	}

	/*

	public function getdatas()
	{
		return $this->datas;
	}

	public function gettimestamp()
	{
		return $this->timestamp;
	}


	public function setdatas($datas)
	{
		$this->datas = $datas;
	}

	public function settimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}
	*/


}
<?php
namespace OCFram;

abstract class Manager
{
  protected $dao;
  
  public function __construct($dao)
  {
  	//var_dump($dao);
    $this->dao = $dao;
  }
}
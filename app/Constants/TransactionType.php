<?php

namespace App\Constants;

class TransactionType
{

   const CREDIT = 'credit';
   const DEBIT = 'debit';
 
   public function getConstants()
   {
      $reflectionClass = new \ReflectionClass($this);
      return $reflectionClass->getConstants();
   }

   public function hasConstant($constans)
   {
      $reflectionClass = new \ReflectionClass($this);
      return $reflectionClass->hasConstant($constans);
   }
   
}

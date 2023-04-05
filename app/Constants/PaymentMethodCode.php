<?php

namespace App\Constants;

class PaymentMethodCode
{

   const WALLET = "wallet";
   const CARD = 'card';
   const APPLEPAY = 'applepay';
   const COD = 'cod';

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

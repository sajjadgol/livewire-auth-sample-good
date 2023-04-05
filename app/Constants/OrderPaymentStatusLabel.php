<?php

namespace App\Constants;

class OrderPaymentStatusLabel
{

   const pending = 'primary';
   const hold = 'primary';
   const completed = 'success';
   const cancelled = 'danger';
   const failed = 'danger';
   const declined = 'secondary';
   const refunded = 'default';
   const refunded_fail = 'warning';

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

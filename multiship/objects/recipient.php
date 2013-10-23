<?php

// Объект - Получатель
class MultiShip_Recipient extends MultiShip_Object
{
  var $_prefix = "recipient_";
  var $_fields = array("first_name", "middle_name", "last_name", "phone", "email", "comment", "time_from", "time_to");
  var $_critical = array("first_name", "last_name", "phone");

  function fixField($name, $value)
  {
    switch ($name)
    {
      case "phone":
      {
        if(is_array($value))
        {
          foreach($value as $key => $phone)
          {
            $value[$key] = preg_replace("/[^0-9]/", '', $phone);
          }
        }
        else
        {
          $value = preg_replace("/[^0-9]/", '', $value);
        }
      }
    }
    return parent::fixField($name, $value);
  }
}

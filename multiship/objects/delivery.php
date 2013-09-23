<?php

// Объект - параметры доставки
class MultiShip_Delivery extends MultiShip_Object
{
  var $_prefix = "delivery_";
  var $_fields = array("direction", "delivery", "price", "pickuppoint", "to_ms_warehouse");
  var $_critical = array("direction", "delivery", "price", "to_ms_warehouse");
}

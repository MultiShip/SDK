<?php

// Объект - Адрес доставки
class MultiShip_DeliveryPoint extends MultiShip_Object
{
  var $_prefix = "deliverypoint_";
  var $_fields = array("index", "city", "street", "house", "build", "housing", "porch", "code", "floor", "flat", "station");
  var $_critical = array("city", "street", "house");
}

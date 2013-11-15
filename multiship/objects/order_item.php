<?php

// Объект - Товарная позиция
class MultiShip_OrderItem extends MultiShip_Object
{
  var $_prefix = "orderitem_";
  var $_fields = array("article", "name", "quantity", "cost", "weight", "width", "height", "length", "id");
  var $_critical = array("name", "quantity", "cost");
}

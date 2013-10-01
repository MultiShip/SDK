<?php

// Объект - Товарная позиция
class MultiShip_OrderItem extends MultiShip_Object
{
  var $_prefix = "orderitem_";
  var $_fields = array("article", "name", "quantity", "cost", "weight", "width", "height", "length");
  var $_critical = array("name", "quantity", "cost");
  var $_not_empty = array("name", "quantity");
}

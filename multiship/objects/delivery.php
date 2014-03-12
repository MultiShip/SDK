<?php

// Объект - параметры доставки
class MultiShip_Delivery extends MultiShip_Object
{
  var $_prefix = "delivery_";
  var $_fields = array("direction", "delivery", "price", "pickuppoint", "to_ms_warehouse");
  var $_critical = array("direction", "delivery", "price", "to_ms_warehouse", "pickuppoint");

  /**
   * @param Multiship_Order|null $order
   * @return bool
   */
  function validate($order = null)
  {
    if (isset($order->user_status_id) && $order->user_status_id == ORDER_DRAFT_STATUS)
    {
      $this->_critical = array();
    }
    else
    {
      $this->_critical = array("direction", "delivery", "price", "to_ms_warehouse", "pickuppoint");
    }
    return parent::validate();
  }
}

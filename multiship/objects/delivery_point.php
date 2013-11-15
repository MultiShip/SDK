<?php

// Объект - Адрес доставки
class MultiShip_DeliveryPoint extends MultiShip_Object
{
  var $_prefix = "deliverypoint_";
  var $_fields = array("index", "city", "street", "house", "build", "housing", "porch", "code", "floor", "flat", "station");
  var $_critical = array("city", "street", "house");

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
      $this->_critical = array("city", "street", "house");
    }
    return parent::validate();
  }

}

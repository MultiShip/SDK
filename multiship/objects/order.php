<?php

/* Объект - Параметры заказа
  @PARAMS:
    (Integer) id ID заказа в системе MultiShip, null для нового заказа
    (String) num Номер заказа в учётной системе магазина
    (String) date Дата заказа в учётной системе магазина
    (Float n,2) weight Вес заказа
    (Float n,2) height Габариты заказа
    (Float n,2) width Габариты заказа
    (Float n,2) length Габариты заказа
    (Integer) payment_method ID пособа оплаты (см. getPaymentMethods())
    (Float n,2) cost Стоимость заказа
    (Float n,2) delivery_cost Стоимость доставки
    (Float n,2) assessed_value Оценочная стоимость заказа
    (Float n,2) total_cost Общая стоимость заказа, взымаемая с получателя
    (String) comment Комментарии к заказу
    (Array Of MultiShip_OrderItem) items опись товарных позиций в заказе
    (Integer) sender ID отправителя
    (Integer) requisite ID реквизитов организации итправителя
    (Integer) warehouse ID склада отправителя
*/

// Объект - Заказ
class MultiShip_Order extends MultiShip_Object
{
  var $_prefix = "order_";
  var $_fields = array("num", "date", "weight", "width", "height", "length", "payment_method", "delivery_cost", "assessed_value", "comment", "items", "sender", "requisite", "warehouse", "user_status_id");
  var $_critical = array("date", "items", "assessed_value", "delivery_cost");

  function __construct()
  {
    parent::__construct();
    defined('ORDER_DRAFT_STATUS') or define('ORDER_DRAFT_STATUS', -2);
  }

  // Добавляем вложение в заказ
  function appendItem(MultiShip_OrderItem $item)
  {
    if (!is_array($this->items))
    {
      $this->items = array();
    }
    $item->appendToArray($this->items[count($this->items)], true);
  }
}

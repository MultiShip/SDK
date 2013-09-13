<?php

/* Объект - Запрос методов доставки
  @PARAMS:
    (String) city_from  Город отправления.
    (String) city_to  Город назначения.
    (String ["todoor"|"pickup"]) delivery_type  Тип доставки (курьер, пвз).
    (Float n,2) weight  Вес посылки.
    (Float n,2) height  Высота посылки.
    (Float n,2) width  Ширина посылки.
    (Float n,2) length  Длина посылки.
*/
class MultiShip_RequestDeliveryList extends MultiShip_Object
{
  var $_fields = array("city_from", "city_to", "weight", "height", "width", "length");
  var $_critical = array("city_from", "city_to", "weight", "height", "width", "length");
}

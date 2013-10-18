<?php
/*
  Пример создания заказа
*/

if (isset($_POST))
{
  define("MULTISHIP_DEBUG", true);

  // Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";
  require_once "../multiship/objects/order.php";
  require_once "../multiship/objects/order_item.php";
  require_once "../multiship/objects/recipient.php";
  require_once "../multiship/objects/delivery.php";
  require_once "../multiship/objects/delivery_point.php";
  require_once "../multiship/objects/request_delivery_list.php";

  // Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();

  // Формируем объекты для создания заказа
  $order = new MultiShip_Order();
  $orderitem = new MultiShip_OrderItem();
  $recipient = new MultiShip_Recipient();
  $delivery = new MultiShip_Delivery();
  $delivery_point = new MultiShip_DeliveryPoint();

  // Заполняем объекты данными из формы
  foreach ($_POST as $key => $arg)
  {
    if (strpos($key, 'order_item') === 0)
    {
      $key = substr($key, 11);
      if (isset($orderitem->{$key}))
        $orderitem->{$key} = $arg[0];
      continue;
    }
    // Ключ имеет формат <имя объекта>_<имя поля> - разбиваем и инициализируем объекты
    $key = explode("_", $key, 2);
    if ($key[0] == "order")
      if (isset($order->{$key[1]}))
        $order->{$key[1]} = $arg;

    if ($key[0] == "recipient")
      if (isset($recipient->{$key[1]}))
        $recipient->{$key[1]} = $arg;
    if ($key[0] == "delivery")
      if (isset($delivery->{$key[1]}))
        $delivery->{$key[1]} = $arg;
    if ($key[0] == "deliverypoint")
      if (isset($delivery_point->{$key[1]}))
        $delivery_point->{$key[1]} = $arg;
  }

  $order->requisite = isset($ms_api->requisite_id[0]) ? $ms_api->requisite_id[0] : 0;
  $order->warehouse = isset($ms_api->warehouse_id[0]) ? $ms_api->warehouse_id[0] : 0;
  $order->sender = isset($ms_api->sender_id[0]) ? $ms_api->sender_id[0] : 0;

  if (isset($_POST['searchDeliveries']))
  {
    echo "Выберите вариант доставки";
  }

  // Создаём заказ, если нажата кнопка сохранения заказа
  if (isset($_POST['createOrder']))
  {
    $order->appendItem($orderitem);

    $order_new = $ms_api->createOrder($order, $recipient, $delivery, $delivery_point);

    if ($order_new == false && $ms_api->_error)
    {
      echo $ms_api->_error;
    }

    // Если заказ успешно сохранился - обновляем данные формы
    if ($order_new->status == "ok")
    {
      foreach ($order_new->data as $key => $value)
      {
        $_POST[$key] = $value;
      }
      echo "Заказ успешно создан. ID заказа: " . $order_new->data->order_id;
    }
    elseif ($order_new->status == "error")
    {
      echo "Ошибка: " . $order_new->data;
    }
  }

  // Собираем список вариантов доставки (см. deliverySearch.php для подробного описания)
  $delivery_list_request = new MultiShip_RequestDeliveryList(@$_POST['sentfrom_city'], @$_POST['deliverypoint_city'], @$_POST['order_weight'], @$_POST['order_width'], @$_POST['order_height'], @$_POST['order_length']);
  $delivery_list_request->delivery_type = "todoor";
  $to_door = $ms_api->searchDeliveryList($delivery_list_request, $delivery_point);
  $delivery_list_request->delivery_type = "pickup";
  $pickup = $ms_api->searchDeliveryList($delivery_list_request);

  $delivery_list_view = '';
  if (is_array($to_door->data))
  {
    foreach ($to_door->data as $variant)
    {
      $delivery_list_view .= '<li onclick="setDeliveryParam(this);" class="delivery_variant" delivery="' . $variant->delivery_id . '" direction="' . $variant->direction_id . '" price="' . $variant->price_id . '" pickuppoint="0" cost="' . $variant->cost . '">' . $variant->delivery_name . " (" . $variant->cost . " руб.)<br/><b>Доставка до двери</b></li>";
    }
  }
  if (is_array($pickup->data))
  {
    foreach ($pickup->data as $variant)
    {
      $delivery_list_view .= '<li onclick="setDeliveryParam(this);" class="delivery_variant" delivery="' . $variant->delivery_id . '" direction="' . $variant->direction_id . '" price="' . $variant->price_id . '" pickuppoint="' . $variant->pickuppoint_id . '" cost="' . $variant->cost . '">' . $variant->delivery_name . " (" . $variant->cost . " руб.)<br/>" . $variant->address . "</li>";
    }
  }
}

// Выводим форму создания заказа
?>
<script type="text/javascript">
  function reCalcValues() {
    var sum = parseFloat(document.getElementById('order_item_quantity_0').value) * parseFloat(document.getElementById('order_item_cost_0').value);
    var from_client = sum + parseFloat(document.getElementById('delivery_cost').value);
    document.getElementById('order_cost').value = sum || '';
    document.getElementById('order_total_cost').value = document.getElementById('order_payment_method_1').checked ? from_client || '' : '0';
  }
</script>
<link rel="stylesheet" href="css/multiship.css"/>
<form action='' method='post'>
  <table class="wide">
    <tr>
      <th colspan=2>
        <fieldset class='wide block' style='float:left;'>
          <legend>Данные заказа</legend>
          <div style="overflow-y:scroll;overflow-x:hidden;float:left;height:300px;">
            <legend>Вложения <a href="#" id="add">+</a></legend>
            <fieldset class='block goods'>
              <legend>Товарная позиция <a href="#" class="sub">-</a></legend>
              Арт
              <input name='order_item_article[0]' value='<?= isset($_POST['order_item_article'][0]) ? $_POST['order_item_article'][0] : 'abc' ?>'><br/>
              Наим.
              <input name='order_item_name[0]' value='<?= isset($_POST['order_item_name'][0]) ? $_POST['order_item_name'][0] : 'товар' ?>'><br/>
              К-во
              <input id="order_item_quantity_0" name='order_item_quantity[0]' value='<?= isset($_POST['order_item_quantity'][0]) ? $_POST['order_item_quantity'][0] : '2' ?>' onkeyup="reCalcValues()"/><br/>
              Цена
              <input id="order_item_cost_0" name='order_item_cost[0]' value='<?= isset($_POST['order_item_cost'][0]) ? $_POST['order_item_cost'][0] : '1200' ?>' onkeyup="reCalcValues()"/><br/>
            </fieldset>
          </div>
          <fieldset>
            <legend>Параметры заказа</legend>
            MS ID <input readonly='readonly' value='<?= @$_POST['order_id']; ?>'><br/>
            Номер <input name='order_num' value='<?= isset($_POST['order_num']) ? $_POST['order_num'] : 'S123' ?>'><br/>
            Дата
            <input name='order_date' value='<?= isset($_POST['order_date']) ? $_POST['order_date'] : '2013-07-01' ?>'><br/>
            Вес
            <input name='order_weight' value='<?= isset($_POST['order_weight']) ? $_POST['order_weight'] : '0.75' ?>'><br/>
            Габариты
            <input class="mini" name='order_width' value='<?= isset($_POST['order_width']) ? $_POST['order_width'] : '10' ?>'><input class="mini" name='order_height' value='<?= isset($_POST['order_height']) ? $_POST['order_height'] : '20' ?>'><input class="mini" name='order_length' value='<?= isset($_POST['order_length']) ? $_POST['order_length'] : '30' ?>'>
          </fieldset>
          <fieldset>
            <legend>Оплата</legend>
            Оценка
            <input name='order_assessed_value' value='<?= isset($_POST['order_assessed_value']) ? $_POST['order_assessed_value'] : '2400' ?>'><br/>
            Доставка
            <input id="delivery_cost" name='order_delivery_cost' value='<?= isset($_POST['order_delivery_cost']) ? $_POST['order_delivery_cost'] : '200' ?>' onkeyup="reCalcValues()"><br/>
            Сумма
            <input id="order_cost" name="order_cost" readonly="readonly" value="<?= isset($_POST['order_cost']) ? $_POST['order_cost'] : '2400' ?>"><br/>
            Итого
            <input id="order_total_cost" name="order_total_cost" readonly="readonly" value="<?= isset($_POST['order_total_cost']) ? $_POST['order_total_cost'] : '2600' ?>"><br/>
            <input id="order_payment_method_1" type="radio" name="order_payment_method" value="1"<?= @$_POST['order_payment_method'] != 3 ? " checked" : "" ?> onclick="reCalcValues()"> наличные
            <input type="radio" name="order_payment_method" value='3'<?= @$_POST['order_payment_method'] == 3 ? " checked" : "" ?> onclick="reCalcValues()"> предоплата<br/>
          </fieldset>
          Комментарии<br/>
          <textarea name='order_comment'><?= isset($_POST['order_comment']) ? $_POST['order_comment'] : '' ?></textarea>
        </fieldset>
        <fieldset class='middle block' style='float: right;'>
          <legend>Данные доставки</legend>
          <ul><?= isset($delivery_list_view) ? $delivery_list_view : "" ?></ul>
          <input type="hidden" id="direction" name='delivery_direction' value='<?= @$_POST['delivery_direction']; ?>'>
          <input type="hidden" id="delivery" name='delivery_delivery' value='<?= @$_POST['delivery_delivery']; ?>'>
          <input type="hidden" id="price" name='delivery_price' value='<?= isset($_POST['delivery_price']) && is_string($_POST['delivery_price']) ? $_POST['delivery_price'] : '' ?>'>
          <input type="hidden" id="pickuppoint" name='delivery_pickuppoint' value='<?= isset($_POST['delivery_pickuppoint']) ? $_POST['delivery_pickuppoint'] : '' ?>'>
          <input type="radio" name='delivery_to_ms_warehouse' value='1' <?= @$_POST['delivery_to_ms_warehouse'] == 1 ? "checked" : ""; ?>> на склад MultiShip
          <br/>
          <input type="radio" name='delivery_to_ms_warehouse' value='0' <?= @$_POST['delivery_to_ms_warehouse'] == 0 ? "checked" : ""; ?>> на склад службы доставки
        </fieldset>
        <fieldset class='middle block' style='float: right;'>
          <legend>Направление доставки</legend>
          Из города
          <input name='sentfrom_city' value='<?= isset($_POST['sentfrom_city']) ? $_POST['sentfrom_city'] : 'Москва' ?>'><br/>
          В город
          <input name='deliverypoint_city' value='<?= isset($_POST['deliverypoint_city']) ? $_POST['deliverypoint_city'] : 'Киров' ?>'><br/>
          на улицу
          <input name='deliverypoint_street' value='<?= isset($_POST['deliverypoint_street']) ? $_POST['deliverypoint_street'] : 'Ленина' ?>'><br>
          в дом
          <input name='deliverypoint_house' value='<?= isset($_POST['deliverypoint_house']) ? $_POST['deliverypoint_house'] : '100А' ?>'><br>
          с индексом
          <input name='deliverypoint_index' value='<?= isset($_POST['deliverypoint_index']) ? $_POST['deliverypoint_index'] : '610002' ?>'><br>
        </fieldset>
        <fieldset class='middle block' style='float: right; margin-bottom: -10px;'>
          <legend>Данные получателя</legend>
          Фамилия
          <input name='recipient_last_name' value='<?= isset($_POST['recipient_last_name']) ? $_POST['recipient_last_name'] : 'Иванов' ?>'><br/>
          Имя
          <input name='recipient_first_name' value='<?= isset($_POST['recipient_first_name']) ? $_POST['recipient_first_name'] : 'Пётр' ?>'><br/>
          Отчество
          <input name='recipient_middle_name' value='<?= isset($_POST['recipient_middle_name']) ? $_POST['recipient_middle_name'] : '' ?>'><br/>
          Телефон
          <input name='recipient_phone' value='<?= isset($_POST['recipient_phone']) ? $_POST['recipient_phone'] : '+7(912)587-45-69' ?>'><br/>
          E-Mail
          <input name='recipient_email' value='<?= isset($_POST['recipient_email']) ? $_POST['recipient_email'] : '' ?>'><br/>
          Время доставки от
          <input name='recipient_time_from' value='<?= isset($_POST['recipient_time_from']) ? $_POST['recipient_time_from'] : '10:00' ?>'><br/>
          до
          <input name='recipient_time_to' value='<?= isset($_POST['recipient_time_to']) ? $_POST['recipient_time_to'] : '17:00' ?>'><br/>
          Комментарии <br/>
          <textarea name='recipient_comment'><?= isset($_POST['recipient_comment']) ? $_POST['recipient_comment'] : '' ?></textarea>
        </fieldset>
        <input type='submit' class='middle submit' name='searchDeliveries' value='Искать варианты доставки' style='float: right; margin-top: 20px;'>
        <input type='submit' class='middle submit' name='createOrder' value='Создать'<?= $_POST ? '' : ' disabled="disabled"' ?> style='float: right; margin-top: 20px;'>
      </th>
    </tr>
  </table>
</form>
<script>
  var lastNode;
  function setDeliveryParam(node) {
    document.getElementById("direction").value = node.getAttribute("direction");
    document.getElementById("delivery").value = node.getAttribute("delivery");
    document.getElementById("price").value = node.getAttribute("price");
    document.getElementById("pickuppoint").value = node.getAttribute("pickuppoint");
    document.getElementById("delivery_cost").value = node.getAttribute("cost");
    reCalcValues();
    if (lastNode) {
      lastNode.className = 'delivery_variant';
    }
    node.className = 'delivery_variant_active';
    lastNode = node;
  }
</script>
<?php
// DEBUG выводим отладочную информацию
if (defined("MULTISHIP_DEBUG") && MULTISHIP_DEBUG)
{
  echo "<div class='debug_panel'><br/>";
  echo nl2br($ms_api->_debug);
  echo "</div>";
}
?>

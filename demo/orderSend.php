<?php
/*
  Пример orderSend
*/
header("Content-type: text/html; charset=utf-8");

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";

  /// Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();

  $order_id_1 = trim($_POST['order_id_1']);
  $text_1 = $_POST['text_1'];
  $parcel_id = null;
  if ($_POST['prepareOrder'] && is_numeric($order_id_1))
  {
    /// подготавливаем заказ к отправке (помещаем его в отгрузку)
    $ms_api->_data['order_id'] = $order_id_1;
    $response = $ms_api->request('confirmSenderOrder');

    if ($response->status == 'ok')
    {
      $text_1 = "Заказ подготовлен к отправке. Теперь его можно отправить.";
      $parcel_id = $response->data->parcel_id;
    }
    else
    {
      $text_1 = $response->data;
    }
  }

  $order_id_2 = trim($_POST['order_id_2']);
  $text_2 = $_POST['text_2'];
  if ($_POST['sendOrder'] && is_numeric($order_id_2))
  {
    /// получив номер заказа и поместив его в отгрузку, можем отправить заказ в службу доставки
    $ms_api->_data['order_id'] = $order_id_2; /// можно id нескольких заказов через запятую
    $ms_api->_data['date_shipment'] = time();
    $response = $ms_api->request('confirmSenderOrders');

    if ($response->status == 'ok')
    {
      $text_2 = "Заказ успешно отправлен в службу доставки.";
      $text_2 .= " Номер отгрузки: " . implode(',', $response->data);
    }
    else
    {
      $text_2 = $response->data;
    }
  }
}
?>

<link rel="stylesheet" href="css/multiship.css"/>
<form method="POST">
  <input type="text" name="order_id_1" placeholder="ID заказа" value="<?php echo isset($order_id_1) ? $order_id_1 : "" ?>"/>
  <input name="prepareOrder" type="submit" value="Поместить заказ в отгрузку"/>
  <input name="text_1" type="hidden" value="<?php echo $text_1 ?>"/>

  <p>
    <?php echo isset($order_id_1) ? "Заказ: " . $order_id_1 : "" ?><br>
    <?php echo isset($text_1) ? $text_1 : "" ?>
  </p>
  <input type="text" name="order_id_2" placeholder="ID заказа" value="<?php echo isset($order_id_2) ? $order_id_2 : "" ?>"/>
  <input name="sendOrder" type="submit" value="Отправить заказ в службу доставки"/>
  <input name="text_2" type="hidden" value="<?php echo isset($text_2) ? $text_2 : "" ?>"/>

  <p><?php echo isset($text_2) ? $text_2 : "" ?></p>
</form>
<?php
// DEBUG выводим отладочную информацию
if (defined("MULTISHIP_DEBUG") && MULTISHIP_DEBUG)
{
  echo "<div class='debug_panel'><br/>";
  echo nl2br($ms_api->_debug);
  echo "</div>";
}
?>

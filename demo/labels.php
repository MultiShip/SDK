<?php
/*
  Пример labels
*/
header("Content-type: text/html; charset=utf-8");

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";

  // Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();
  $order_id = trim($_POST['order_id']);
  /// ярлык к заказу
  if (is_numeric($order_id))
  {
    $ms_api->getSenderOrderLabel($order_id);
  }
  /// сопроводительные документы к отгрузке
  $parcel_id = trim($_POST['parcel_id']);
  if (is_numeric($parcel_id))
  {
    $ms_api->getSenderParcelLabel($parcel_id);
  }
}
?>

<link rel="stylesheet" href="css/multiship.css"/>
<form method="POST">
  <input type="text" placeholder="ID заказа" name="order_id">
  <input type="submit" value="Ярлык">
</form>
<form method="POST">
  <input type="text" placeholder="ID отгрузки" name="parcel_id">
  <input type="submit" value="Сопроводительные документы">
</form>
<?php
/// DEBUG выводим отладочную информацию
if (defined("MULTISHIP_DEBUG") && MULTISHIP_DEBUG)
{
  echo "<div class='debug_panel'><br/>";
  echo nl2br($ms_api->_debug);
  echo "</div>";
}
?>

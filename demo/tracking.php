<?php
/*
  Пример tracking
*/
header("Content-type: text/html; charset=utf-8");

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";

  /// Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();
  $status = null;

  $order_id_1 = trim($_POST['order_id_1']);
  /// Последниий статус заказа
  if (is_numeric($order_id_1))
  {
    $response = $ms_api->getSenderOrderStatus($order_id_1);
    if ($response->status == 'ok')
    {
      $status = $response->data;
    }
  }
  /// Все статусы заказа
  $order_id_2 = trim($_POST['order_id_2']);
  $statuses_view = '';
  if (is_numeric($order_id_2))
  {
    $response = $ms_api->getSenderOrderStatuses($order_id_2);
    if ($response->status == 'ok')
    {
      $statuses = $response->data->data;
      if (is_array($statuses))
      {
        foreach ($statuses as $one)
        {
          $statuses_view .= '<li>' . date('Y-m-d H:i:s', $one->time) . ' - ' . $one->status . '</li>';
        }
      }
    }
  }
}
?>

<link rel="stylesheet" href="css/multiship.css">
<form method="POST">
  <p>Последниий статус заказа:</p>
  <input type="text" placeholder="ID заказа" name="order_id_1" value="<?php echo isset($_POST['order_id_1']) ? $_POST['order_id_1'] : '' ?>">
  <input type="submit" value="Узнать статус">

  <p><b>Статус заказа: </b><?php echo isset($status) ? $status : "нет данных" ?></p>
  <br>

  <p>Все статусы заказа:</p>
  <input type="text" placeholder="ID заказа" name="order_id_2" value="<?php echo isset($_POST['order_id_2']) ? $_POST['order_id_2'] : '' ?>">
  <input type="submit" value="Получить cписок статусов">
</form>
<p><b>Статусы заказа:</b></p>
<table>
  <tbody>
  <tr>
    <td>
      <ul>
        <?php echo isset($statuses_view) ? $statuses_view : "нет данных" ?>
      </ul>
    </td>
  </tr>
  </tbody>
</table>

<?php
// DEBUG выводим отладочную информацию
if (defined("MULTISHIP_DEBUG") && MULTISHIP_DEBUG)
{
  echo "<div class='debug_panel'><br/>";
  echo nl2br($ms_api->_debug);
  echo "</div>";
}
?>

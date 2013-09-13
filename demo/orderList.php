<?php
/*
  Пример получения списка заказов
*/

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";

  /// Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();

  $limit = $_POST['limit'];

  /// Получаем список всех реальных и отправленных заказов, и преобразуем его в массив для удобства вывода
  $orders_list = $ms_api->getSenderOrders(false, 0, 0, $_POST['limit']);
  $orders_list->data = (array)$orders_list->data;

  /// Получить только нужные заказы по их ID
  /// $orders_list = $ms_api->getSenderOrders([1000, 2000, 3000]);

  /// Готовим к выводу список заказов в виде <li> списка HTML
  $orders_view = '';
  if (is_array($orders_list->data))
  {
    $orders_count = count($orders_list->data);
    foreach ($orders_list->data as $order)
    {
      $orders_view .= "<li>" . $order->num . '-MS' . $order->id . " (от " . $order->created . ") на сумму " . $order->total_cost . " руб.</li>";
    }
  }
}

/// Выводим список заказов
?>

<link rel="stylesheet" href="css/multiship.css"/>
<form action='' method='post'>
  <table>
    <tr>
      <th>
        Список заказов ваших магазинов
        <hr/><?= isset($orders_count) ? $orders_count : '0' ?> заказ(а/ов)
      </th>
    </tr>
    <tr>
      <td>
        <ul>
          <?= isset($orders_view) ? $orders_view : "" ?>
        </ul>
      </td>
    </tr>
    <tr>
      <td>
        Выводить последние
        <input name='limit' value='<?= isset($_POST['limit']) ? $_POST['limit'] : '10' ?>' style="width: 50px;"/>
        <input type='submit' class='submit' value='Искать' style='float: right;'>
      </td>
    </tr>
  </table>
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

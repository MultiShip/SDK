<?php
/*
  Пример получения списка вариантов доставки до двери и до ПВЗ
*/

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";
  require_once "../multiship/objects/request_delivery_list.php";

  /// Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();

  /// Формируем запрос на получение списка вариантов с направлением и габаритами переданными через форму
  $delivery_list_request = new MultiShip_RequestDeliveryList(@$_POST['from'], @$_POST['to'], @$_POST['w'], @$_POST['x'], @$_POST['y'], @$_POST['z']);

  /// Создаём запрос на получение списка вариантов доставки "до двери" для аналогичных товара и направления
  $delivery_list_request->delivery_type = "todoor";

  /// Получаем список вариантов доставки "до двери" для перевозки груза с указанными в заявке параметрами в указанном в заявке направления
  $to_door = $ms_api->searchDeliveryList($delivery_list_request);

  /// Создаём запрос на получение списка вариантов доставки "до пвз" для аналогичных товара и направления
  $delivery_list_request->delivery_type = "pickup";

  /// Получаем список вариантов доставки до "ПВЗ"
  $pickup = $ms_api->searchDeliveryList($delivery_list_request);

  $todoor_count = $pickup_count = 0;

  /// Готовим к выводу результаты поиска вариантов доставки "до двери" в виде <li> списка HTML
  $todoor_view = '';
  if (is_array($to_door->data))
  {
    $todoor_count = count($to_door->data);
    foreach ($to_door->data as $variant)
    {
      $todoor_view .=  '<li>' . $variant->delivery_name . " (" . $variant->cost . " руб.)</li>";
    }
  }

  /// Готовим к выводу результаты поиска вариантов доставки "до ПВЗ" в виде <li> списка HTML
  $pickup_view = '';
  if (is_array($pickup->data))
  {
    $pickup_count = count($pickup->data);
    foreach ($pickup->data as $variant)
    {
      $pickup_view .= '<li>' . $variant->delivery_name . " (" . $variant->cost . " руб.)<br/>" . $variant->address . "</li>";
    }
  }
}

/// Выводим форму для запроса списка вариантов доставки и результаты поиска
?>

<link rel="stylesheet" href="css/multiship.css"/>
<form action='' method='post'>
  <table>
    <tr>
      <th colspan=2>
        <fieldset class='block' style='float:left;'>
          <legend>Габариты</legend>
            X, см <input name='x' value='<?= isset($_POST['x']) ? $_POST['x'] : '10' ?>'><br/>
            Y, см <input name='y' value='<?= isset($_POST['y']) ? $_POST['y'] : '20' ?>'><br/>
            Z, см <input name='z' value='<?= isset($_POST['z']) ? $_POST['z'] : '30' ?>'><br/>
            Вес, кг <input name='w' value='<?= isset($_POST['w']) ? $_POST['w'] : '0.75' ?>'>
        </fieldset>
        <fieldset class='block'  style='float:right;'>
          <legend>Направление доставки</legend>
            Из <input name='from' value='<?= isset($_POST['from']) ? $_POST['from'] : 'Москва' ?>'><br/>
            До <input name='to' value='<?= isset($_POST['to']) ? $_POST['to'] : 'Киров' ?>'>
        </fieldset>
        <input type='submit' class='submit' value='Искать' style='float: right;'>
      </th>
    </tr>
    <tr>
      <th width='50%'>
        Доставка до двери <hr/><?= $todoor_count ? $todoor_count : '0' ?> вариант(а/ов)
      </th>
      <th>
       Доставка до ПВЗ <hr/><?= $pickup_count ? $pickup_count : '0' ?> вариант(а/ов)
      </th>
    </tr>
    <tr>
      <td>
        <ul><?= $todoor_view ?></ul>
      </td>
      <td>
        <ul><?= $pickup_view ?></ul>
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

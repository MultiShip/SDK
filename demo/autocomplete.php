<?php
/*
  Пример autocomplete
*/
header("Content-type: text/html; charset=utf-8");

if ($_POST)
{
  define("MULTISHIP_DEBUG", true);

  /// Подключаем необходимые библиотеки
  require_once "../multiship/multiship.php";

  // Получаем рабочий экземпляр API
  $ms_api = MultiShip::init();
  $text = $_POST['text'];
  $city = $_POST['city_name'];
  if ($_POST['city_name'] && !$_POST['deliverypoint_street'])
  {
    $type = 'city';
    $answer = $ms_api->autocomplete($city, $type);

    if ($answer->status == 'ok')
    {
      echo '<ul>';
      foreach ($answer->data as $key => $val)
      {
        echo '<li>' . $val->label . '</li>';
      }
      echo '</ul>';
    }
    else
    {
      $text = $answer->data;
    }
  }
  else
  {
    $street = $_POST['deliverypoint_street'];
    $type = 'street';
    $answer = $ms_api->autocomplete($street, $type, $city);
    if ($answer->status == 'ok')
    {
      echo '<ul>';
      foreach ($answer->data as $key => $val)
      {
        echo '<li>' . $val->label . '</li>';
      }
      echo '</ul>';
    }
    else
    {
      $text = $answer->data;
    }
  }
}
?>
<link rel="stylesheet" href="css/multiship.css"/>
<form method="POST">
  <input type="text" placeholder="Город" name="city_name">
  <input type="text" placeholder="Улица" name="deliverypoint_street">
  <input name="text" type="hidden" value="<?php echo isset($text) ? $text : "" ?>"/>
  <input type="submit" value="Варианты автодополнения">
</form>

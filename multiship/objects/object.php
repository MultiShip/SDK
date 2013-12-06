<?php

// Шаблон валидируемого объекта-запроса (набора параметров)
class MultiShip_Object
{
  // (String) Префикс, используемый для всех полей объекта
  var $_prefix = "";
  // (Array of Strings) Список полей объекта
  var $_fields = array();
  // (Array of Strings) Список полей, обязательных для заполнения
  var $_critical = array();
  // (Array of Strings) После валидации - список пустых полей, обязательных для заполнения
  var $_critical_empty = array();
  // (Array of Strings) Список валидируемых полей и соответствующих им валидаторов (regExp)
  var $_validation = array();
  // (Array of Strings) После валидации - список полей, не прошедших валидацию
  var $_validation_wrong = array();
  // (String) Последняя возникшая ошибка (текстом)
  var $_last_error = "";
  // (Mixed) Дополнительные данные по последней вохзникшей ошибке
  var $_error_data = array();

  /*
  Пытаемся исправить некорректный формат ввода в поля
  @PARAMS
    (String) name - имя поля
    (Mixed) value - значение поля для исправления формата
  @RETURN
	(Mixed) value - исправленное значение поля
  */
  function fixField($name, $value)
  {
    return is_string($value) ? trim($value) : $value;
  }

  /*
  Пытаемся исправить некорректный формат данных в полях
  @PARAMS
    нет
  */
  function fixFields()
  {
    foreach ((array)$this as $key => $value)
    {
      // Игнорируем служебные параметры
      if ($key[0] == '_')
      {
        continue;
      }
      // Вызываем CallBack дял очистки поля
      $this->{$key} = $this->fixField($key, $value);
    }
  }

  /*
  Проверка корректности параметров запроса
  @PARAMS
    нет
  */
  function validate()
  {
    return true; //отключаем валидацию SDK
    $this->_critical_empty = array();
    $this->_validation_wrong = array();

    /// Находим незаполненные поля обязательные для заполнения
    foreach ($this->_critical as $critical)
    {
      if (!isset($this->{$critical}) or $this->{$critical} === "")
      {
        $this->_critical_empty[] = $critical;
      }
    }

    /// Находим неверно заполненные валидируемые поля
    foreach ($this->_validation as $validation => $regexp)
    {
      if (!isset($this->{$validation}) or ($this->{$validation} != "" and !preg_match($regexp, $this->{$validation})))
      {
        $this->_validation_wrong[] = $validation;
      }
    }

    /// Проставляем ошибки валидации, если они обнаружены
    if (count($this->_critical_empty) > 0)
    {
      $this->_last_error = MULTISHIP_ERROR_VALIDATION_EMPTY;
      $this->_error_data = $this->_critical_empty;

      return false;
    }
    elseif (count($this->_validation_wrong) > 0)
    {
      $this->_last_error = MULTISHIP_ERROR_VALIDATION;
      $this->_error_data = $this->_validation_wrong;

      return false;
    }

    /// Если ошибки не обнаружены - заявляем об успешном завершении работы
    $this->_last_error = MULTISHIP_ERROR_SUCCESS;
    $this->_error_data = array();

    return true;
  }

  /*
  Добавление параметров объекта к массиву параметров
  @PARAMS
    (Pointer to Array of Mixed) ^arr - ссылка на массив параметров
    (Boolean) replace - указывает, требуется дописать параметры в исходный объект (true), или создать новый (false)
    (Multiship_Order) order - экземпляр класса Multiship_Order, необходим для корректной валидации заказа-черновика
  */
  function appendToArray(&$arr, $replace = false, $order = null)
  {
    // Создаём рабочую копию исходного массива
    $arr_result = $arr;

    // Пытаемся исправить неправильные форматы полей
    $this->fixFields();

    // Если объект не проходит валидацию, то присоединять к общему списку параметров его нельзя - ничего не делаем
    if (!$this->validate($order))
    {
      return false;
    }

    // Добавляем параметры объекта-запроса к массиву параметров
    foreach ((array)$this as $key => $value)
    {
      // Игнорируем служебные параметры
      if ($key[0] == '_')
      {
        continue;
      }

      $arr_result[$this->_prefix . $key] = $value;
    }

    // Если требуется переписать исходный массив - записываем на его место рабочий массив
    if ($replace)
    {
      $arr = $arr_result;
    }

    return $arr_result;
  }

  /*
  Инициализация объекта
  @PARAMS
    (...) values - значение полей объекта в порядке, указанном в fields, для начальной инициализации объекта.
    Если число параметров меньше числа полей - оставшиеся fields инициализируются пустыми значениями
  */
  function __construct()
  {
    $count_params = func_num_args();
    foreach ($this->_fields as $id => $field)
    {
      $this->{$field} = $count_params > $id ? func_get_arg($id) : '';
    }
  }

}

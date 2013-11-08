<?php

class MultiShip_OpenApi extends MultiShip_Object
{
  var $_data = array();
  var $_result = "";
  var $_error = "";
  var $_debug = "";

  var $client_id = '';
  var $sender_id = array();
  var $warehouse_id = array();
  var $requisite_id = array();
  var $api_url = '';
  var $format = '';
  var $keys = array();

  /*
  Инициализируем настройки
  @PARAMS
    нет
  */
  function __construct($settings)
  {
    foreach ($settings as $key => $value)
    {
      if (isset($this->{$key}))
      {
        $this->{$key} = $value;
      }
    }
  }

  /*
  Получение списка методов оплаты
  @PARAMS
    нет
  */
  function getPaymentMethods()
  {
    $this->_data = array();

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('getPaymentMethods');
  }

  /*
  Получение списка способов доставки
  @PARAMS
    нет
  */
  function getDeliveryMethods()
  {
    $this->_data = array();

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('getDeliveryMethods');
  }

  /*
  Получение списка вариантов доставки
  @PARAMS:
    (MultiShip_RequestDeliveryList) request - запрос на получение списков вариантов доставки
    (MultiShip_DeliveryPoint) deliverypoint - целевая точка доставки для более точного поиска вариантов "до двери"
  */
  function searchDeliveryList($request, $deliverypoint = null)
  {
    $this->_data = array();

    // проверяем входные данные
    if (!$request instanceof MultiShip_RequestDeliveryList)
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }
    if ($deliverypoint != null && !$deliverypoint instanceof MultiShip_DeliveryPoint)
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }
    if (!$request->validate())
    {
      $this->_error = $request->_last_error;

      return false;
    }

    // Инициализируем необходимые поля запроса
    $request->appendToArray($this->_data, true);
    if ($deliverypoint != null)
    {
      $deliverypoint->appendToArray($this->_data, true);
    }

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('searchDeliveryList');
  }

  /*
  Получение списка заказов
  @PARAMS:
    (Array of Integer | Integer | false) orders - номера заказов для загрузки
    (boolean) real - только реальные заказы (если 0, то и реальные, и тестовые)
    (boolean) sent - только отправленные в СД заказы (если 0, то и отправленные, и не отправленные)
    (Array of Integer) deliveries - ID служб доставки
    (Array of Integer) shops - ID магазинов
    (Array of String) statuses - массив UNIFORM-статусов
  */
  function getSenderOrders($orders = false, $real = 1, $sent = 1, $limit = 10, $deliveries = array(), $shops = array(), $statuses = array())
  {
    $this->_data = array();

    // Проверяем входные данные и нициализируем необходимые поля запроса в зависимости от типа аргумента
    $this->_data['real'] = $real;
    $this->_data['sent'] = $sent;
    $this->_data['limit'] = $limit;
    $this->_data['deliveries'] = implode(',', $deliveries);
    $this->_data['shops'] = implode(',', $shops);
    $this->_data['statuses'] = implode(',', $statuses);
    if (is_array($orders))
    {
      $this->_data['order_ids'] = implode(',', $orders);
    }
    elseif (is_numeric($orders))
    {
      $this->_data['order_ids'] = $orders;
    }
    elseif ($orders === false)
    {
      $this->_data['order_ids'] = '';
    }
    else
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('getSenderOrders');
  }

  /*
  Получение статуса заказа
  @PARAMS:
    (MS Number String where ? is Integer ["??"]|["??-MS??"]|["MS??"]]) order_id - номер заказа
  */
  function getSenderOrderStatus($order_id)
  {
    $this->_data = array();

    // Проверяем входные данные и нициализируем необходимые поля запроса
    if (preg_match("@^((\d+)|((\d+\-)?MS\d+))$@", $order_id))
    {
      $this->_data['order_id'] = $order_id;
    }
    else
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('getSenderOrderStatus');
  }

  /*
  Получение истории статусов заказа
  @PARAMS:
    (Integer) order_id - номер заказа
  */
  function getSenderOrderStatuses($order_id)
  {
    $this->_data = array();

    /// Проверяем входные данные и нициализируем необходимые поля запроса
    if (preg_match('/^((\d+)|((\d+\-)?MS\d+))$/', $order_id))
    {
      $this->_data['order_id'] = $order_id;
    }
    else
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }

    /// Отправляем запрос и возвращаем результат запроса
    return $this->request('getSenderOrderStatuses');
  }

  /**
  Создание заказа
  @PARAMS:
  (MultiShip_Order) order - данные заказа
  (MultiShip_Recipient) recipient - данные получателя
  (MultiShip_Delivery) delivery - данные варианта доставки
  (MultiShip_DeliveryPoint) delivery_point - данные точки доставки (адрес получателя или ПВЗ ID)

  @RETURN bool|mixed|object
   */
  function createOrder($order, $recipient, $delivery, $delivery_point)
  {
    $this->_data = array();

    // проверяем входные данные
    if (!$order instanceof MultiShip_Order or !$recipient instanceof MultiShip_Recipient or !$delivery instanceof MultiShip_Delivery or !$delivery_point instanceof MultiShip_DeliveryPoint)
    {
      $this->_error = MULTISHIP_ERROR_WRONG_PARAM;

      return false;
    }
    if (!$order->validate())
    {
      $this->_error = $order->_last_error;

      return false;
    }
    if (!$recipient->validate($order))
    {
      $this->_error = $recipient->_last_error;

      return false;
    }
    if (!$delivery->validate())
    {
      $this->_error = $delivery->_last_error;

      return false;
    }
    if (!$delivery->pickuppoint && !$delivery_point->validate($order))
    {
      $this->_error = $delivery_point->_last_error;

      return false;
    }

    // Инициализируем необходимые поля запроса
    $order->appendToArray($this->_data, true);
    $recipient->appendToArray($this->_data, true);
    $delivery->appendToArray($this->_data, true);
    $delivery_point->appendToArray($this->_data, true);

    // Отправляем запрос и возвращаем результат запроса
    return $this->request('createOrder');
  }

  /*
    Ярлык к заказу
    @PARAMS:
      (Integer) order_id - номер заказа
  */
  function getSenderOrderLabel($order_id, $return = false)
  {
    $this->_data = array();
    $this->_data['order_id'] = $order_id;
    $response = $this->request('getSenderOrderLabel');
    if ($response->status == 'ok')
    {
      $file = base64_decode($response->data);
      if ($return)
      {
        return $file;
      }
      else
      {
        $this->_echoLabel($file, 'label_' . $order_id);
      }
    }
    else
    {
      return false;
    }

    return false;
  }

  /*
    Сопроводительные документы к отгрузке
    @PARAMS:
      (Integer) order_id - номер заказа
  */
  function getSenderParcelLabel($parcel_id, $return = false)
  {
    $this->_data = array();
    $this->_data['parcel_id'] = $parcel_id;
    $response = $this->request('getSenderParcelLabel');
    if ($response->status == 'ok')
    {
      $file = base64_decode($response->data);
      if ($return)
      {
        return $file;
      }
      else
      {
        $this->_echoLabel($file, 'docs_' . $parcel_id);
      }
    }
    else
    {
      return false;
    }

    return false;
  }

  /*
  Получение индекса по адресу
  @PARAMS:
    (String) city - город
    (String) street - улица
    (String) house - дом
  */
  function getIndex($city, $street, $house)
  {
    $this->_data = array(
      'city' => $city,
      'street' => $street,
      'house' => $house,
    );
    return $this->request('getIndex');
  }

  /*
  Автокомплит
  @PARAMS:
    (String) term - вводимые данные
    (String) type - city/street
    (String) city_name -  город
  */
  function autocomplete($term, $type, $city_name = '')
  {
    $this->_data = array(
      'type' => $type,
      'term' => $term,
      'city_name' => $city_name
    );
    return $this->request('autocomplete');
  }

  function _echoLabel($file, $filename)
  {
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Type: application/pdf');
    header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($file, '8bit') : strlen($file)));
    header("Content-Disposition: attachment; filename=\"$filename.pdf\"");
    header('Content-Transfer-Encoding: binary');
    echo $file;
  }


  /*
  Подписывание запроса секретным ключом и ID пользователя
  @PARAMS:
    (String) method - Название подписываемого метода
  */
  function sign($method)
  {
    $hash = '';

    // Добавляем к запросу внутренные параматры для правильного формирования подписи
    $this->_data['secret_key'] = '';
    $this->_data['format'] = $this->format;
    $this->_data['client_id'] = $this->client_id;

    // Сортируем  параметры запроса по ключам в алфавитном порядке для правильного формирования подписи
    $keys = array_keys($this->_data);
    sort($keys);

    // Собираем все параметры запроса до 3 уровня вложенности и генерируем подпись
    foreach ($keys as $key)
    {
      if (!is_array($this->_data[$key]))
      {
        $hash .= $this->_data[$key];
      }
      else
      {
        $subkeys = array_keys($this->_data[$key]);
        sort($subkeys);
        foreach ($subkeys as $subkey)
        {
          if (!is_array($this->_data[$key][$subkey]))
          {
            $hash .= $this->_data[$key][$subkey];
          }
          else
          {
            $subsubkeys = array_keys($this->_data[$key][$subkey]);
            sort($subsubkeys);
            foreach ($subsubkeys as $subsubkey)
            {
              if (!is_array($this->_data[$key][$subkey][$subsubkey]))
              {
                $hash .= $this->_data[$key][$subkey][$subsubkey];
              }
            }
          }
        }
      }
    }
    $hash .= $this->keys[$method];

    if (defined("MULTISHIP_DEBUG") and MULTISHIP_DEBUG == true)
    {
      ob_start();
      echo "-------START OpenAPI::Sign(...);-------\r\n";
      echo "Request Client Key: \r\n" . $this->keys[$method] . "\r\n";
      echo "Request String Dump: \r\n" . $hash . "\r\n";
      echo "Request Secure Key = MD5(Request String Dump): \r\n" . md5($hash) . "\r\n";
      echo "-------END OpenAPI::Sign(...);-------\r\n";
      $this->_debug .= ob_get_clean();
    }

    $hash = md5($hash);

    // Подписываем запрос
    $this->_data['secret_key'] = $hash;
  }

  /*
  Отправка запроса на сервер
  @PARAMS:
    (String) method - Название запрашиваемого метода
  */
  function request($method)
  {
    // Подписываем запрос
    $this->sign($method);

    $request = http_build_query($this->_data);

    // Отправляем запрос на обработку в MultiShip API и сохраняем ответ
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $this->api_url . $method);
    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($curl_handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl_handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl_handle, CURLOPT_POST, 1);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_answer = curl_exec($curl_handle);
    curl_close($curl_handle);

    // Приводим ответ к единому формату (объект)
    if (defined("MULTISHIP_DEBUG") and MULTISHIP_DEBUG == true)
    {
      ob_start();
      echo "-------START OpenAPI::Request(...);-------\r\n";
      echo "REQUEST URL: \r\n" . $this->api_url . $method . "\r\n";
      echo "POST DATA: \r\n";
      print_r($request);
      echo "\r\n";
      echo "CURL ANSWER: \r\n";
      print_r($curl_answer);
      echo "\r\n";
      echo "-------END OpenAPI::Request(...);-------\r\n";
      $this->_debug .= ob_get_clean();
    }

    if ($this->format == 'php')
    {
      $this->_result = (object)unserialize($curl_answer);
    }
    else
    {
      $this->_result = json_decode($curl_answer);
    }

    return $this->_result;
  }

}

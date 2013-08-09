<?php

  $config = new MultiShip_Config();

  $config -> client_id = '';
  $config -> sender_id = array();
  $config -> warehouse_id = array();
  $config -> requisite_id = array();
  $config -> api_url = "https://multiship.ru/OpenAPI_v3/";
  $config -> format = 'json';
  $config -> keys =
    array(
      'getPaymentMethods' => '',
      'getDeliveryMethods' => '',
      'searchDeliveryList' => '',
      'createOrder' => '',
      'confirmSenderOrder' => '',
      'confirmSenderParcel' => '',
      'getSenderOrders' => '',
      'getSenderOrderLabel' => '',
      'getSenderParcelLabel' => '',
      'getSenderOrderStatus' => '',
      'getSenderOrderStatuses' => '',
      'getSenderNomenclature' => '',
      'getSenderGoodsBalans' => '', 
    );
    

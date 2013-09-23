<?php

class MultiShip_Config
{
  var $client_id = '';
  var $sender_id = array();
  var $warehouse_id = array();
  var $requisite_id = array();
  var $api_url = '';
  var $format = '';
  var $keys =
    array(
      'getPaymentMethods' => '',
      'getDeliveryMethods' => '',
      'searchDeliveryList' => '',
      'createOrder' => '',
      'confirmSenderOrder' => '',
      'confirmSenderParcel' => '',
      'confirmSenderOrders' => '',
      'getSenderOrders' => '',
      'getSenderOrderLabel' => '',
      'getSenderParcelLabel' => '',
      'getSenderOrderStatus' => '',
      'getSenderOrderStatuses' => '',
      'getSenderNomenclature' => '',
      'getSenderGoodsBalans' => '',
      'getCities' => '',
      'getIndex' => '',
    );
}

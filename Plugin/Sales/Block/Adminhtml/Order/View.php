<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_DeleteOrders
 * @copyright   Copyright (c) 2019 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
 
namespace Ecomteck\DeleteOrders\Plugin\Sales\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;

class View
{
  /** @var \Magento\Framework\UrlInterface */
  protected $_urlBuilder;

  /** @var \Magento\Framework\AuthorizationInterface */
  protected $_authorization;

  public function __construct(
    UrlInterface $url,
    AuthorizationInterface $authorization
  ) {
    $this->_urlBuilder = $url;
    $this->_authorization = $authorization;
  }

  public function beforeSetLayout(OrderView $view) {
		$message ='Are you sure you want to delete this?';
		$url = $this->_urlBuilder->getUrl('deleteorders/order/deleteOrder', ['id' => $view->getOrderId()]);

		$view->addButton(
		  'ecomteck_deleteorders_adminhtml_order_view_add_button',
		  [
			'label' => __('Delete'),
			'class' => 'ecomteck_deleteorder',
			'onclick' => "confirmSetLocation('{$message}', '{$url}')"
		  ]
		);
  }
}
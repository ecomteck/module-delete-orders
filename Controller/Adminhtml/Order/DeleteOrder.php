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

namespace Ecomteck\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

class DeleteOrder extends AbstractDeleteorder
{
  public $_resource;
  private $deploymentConfig;
  protected $orderRepository;
  
	/**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
	public function __construct(
		Context $context,
		ResourceConnection $resource,
		DeploymentConfig $deploymentConfig,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	){

		$this->_resource = $resource;
		parent::__construct($context);
		$this->deploymentConfig = $deploymentConfig;
		$this->orderRepository = $orderRepository;
	}
	
	public function getOrder($id)
	{
		return $this->orderRepository->get($id);
	}

	public function execute()
    {
		$orderId = $this->getRequest()->getParam('id');
		if($orderId){
			try {
				$order = $this->getOrder($orderId);
				$countCancelOrder = 0;
				$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
				$showTables = $connection->fetchCol('show tables');

				$tblPrefix = (string)$this->deploymentConfig->get(
						ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX
					);
				
				$tblSalesOrder = $connection->getTableName($tblPrefix . 'sales_order');
				$tblSalesCreditmemoComment = $connection->getTableName($tblPrefix . 'sales_creditmemo_comment');
				$tblSalesCreditmemoItem = $connection->getTableName($tblPrefix . 'sales_creditmemo_item');
				$tblSalesCreditmemo = $connection->getTableName($tblPrefix . 'sales_creditmemo');
				$tblSalesCreditmemoGrid = $connection->getTableName($tblPrefix . 'sales_creditmemo_grid');
				$tblSalesInvoiceComment = $connection->getTableName($tblPrefix . 'sales_invoice_comment');
				$tblSalesInvoiceItem = $connection->getTableName($tblPrefix . 'sales_invoice_item');
				$tblSalesInvoice = $connection->getTableName($tblPrefix . 'sales_invoice');
				$tblSalesInvoiceGrid = $connection->getTableName($tblPrefix . 'sales_invoice_grid');
				$tblQuoteAddressItem = $connection->getTableName($tblPrefix . 'quote_address_item');
				$tblQuoteItemOption = $connection->getTableName($tblPrefix . 'quote_item_option');
				$tblQuote = $connection->getTableName($tblPrefix . 'quote');
				$tblQuoteAddress = $connection->getTableName($tblPrefix . 'quote_address');
				$tblQuoteItem = $connection->getTableName($tblPrefix . 'quote_item');
				$tblQuotePayment = $connection->getTableName($tblPrefix . 'quote_payment');
				$tblQuoteShippingRate = $connection->getTableName($tblPrefix . 'quote_shipping_rate');
				$tblQuoteIDMask = $connection->getTableName($tblPrefix . 'quote_id_mask');
				$tblSalesShipmentComment = $connection->getTableName($tblPrefix . 'sales_shipment_comment');
				$tblSalesShipmentItem = $connection->getTableName($tblPrefix . 'sales_shipment_item');
				$tblSalesShipmentTrack = $connection->getTableName($tblPrefix . 'sales_shipment_track');
				$tblSalesShipment = $connection->getTableName($tblPrefix . 'sales_shipment');
				$tblSalesShipmentGrid = $connection->getTableName($tblPrefix . 'sales_shipment_grid');
				$tblSalesOrderAddress = $connection->getTableName($tblPrefix . 'sales_order_address');
				$tblSalesOrderItem = $connection->getTableName($tblPrefix . 'sales_order_item');
				$tblSalesOrderPayment = $connection->getTableName($tblPrefix . 'sales_order_payment');
				$tblSalesOrderStatusHistory = $connection->getTableName($tblPrefix . 'sales_order_status_history');
				$tblSalesOrderGrid = $connection->getTableName($tblPrefix . 'sales_order_grid');
				$tblLogQuote = $connection->getTableName($tblPrefix . 'log_quote');
				$showTablesLog = $connection->fetchCol('SHOW TABLES LIKE ?', '%'.$tblLogQuote);
				$tblSalesOrderTax = $connection->getTableName($tblPrefix . 'sales_order_tax');
				
				if ($order->getIncrementId()) {
					$incId = $order->getIncrementId();
					if (in_array($tblSalesOrder, $showTables)) {
						$result1 = $connection->fetchAll('SELECT quote_id FROM `'.$tblSalesOrder.'` WHERE entity_id='.$orderId);
						$quoteId = (int) $result1[0]['quote_id'];
					}
					$connection->rawQuery('SET FOREIGN_KEY_CHECKS=1');
					if (in_array($tblSalesCreditmemoComment, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesCreditmemoComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId.')');
					}
					if (in_array('sales__creditmemo_item', $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesCreditmemoItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId.')');
					}
					if (in_array($tblSalesCreditmemo, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesCreditmemoGrid, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesCreditmemoGrid.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesInvoiceComment, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesInvoiceComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId.')');
					}
					if (in_array($tblSalesInvoiceItem, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesInvoiceItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId.')');
					}
					if (in_array($tblSalesInvoice, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesInvoiceGrid, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesInvoiceGrid.'` WHERE order_id='.$orderId);
					}
					if ($quoteId) {
						if (in_array($tblQuoteAddressItem, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuoteAddressItem.'` WHERE parent_item_id IN (SELECT address_id FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId.')');
						}
						if (in_array($tblQuoteShippingRate, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuoteShippingRate.'` WHERE address_id IN (SELECT address_id FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId.')');
						}
					   if (in_array($tblQuoteIDMask, $showTables)) {
						   $connection->rawQuery('DELETE FROM `'.$tblQuoteIDMask.'` where quote_id='.$quoteId);
						}
						if (in_array($tblQuoteItemOption, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuoteItemOption.'` WHERE item_id IN (SELECT item_id FROM `'.$tblQuoteItem.'` WHERE quote_id='.$quoteId.')');
						}
						if (in_array($tblQuote, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuote.'` WHERE entity_id='.$quoteId);
						}
						if (in_array($tblQuoteAddress, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId);
						}
						if (in_array($tblQuoteItem, $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuoteItem.'` WHERE quote_id='.$quoteId);
						}
						if (in_array('sales__quotePayment', $showTables)) {
							$connection->rawQuery('DELETE FROM `'.$tblQuotePayment.'` WHERE quote_id='.$quoteId);
						}
					}
					if (in_array($tblSalesShipmentComment, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesShipmentComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId.')');
					}
					if (in_array($tblSalesShipmentItem, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesShipmentItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId.')');
					}
					if (in_array($tblSalesShipmentTrack, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesShipmentTrack.'` WHERE order_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE parent_id='.$orderId.')');
					}
					if (in_array($tblSalesShipment, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesShipmentGrid, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesShipmentGrid.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesOrder, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrder.'` WHERE entity_id='.$orderId);
					}
					if (in_array($tblSalesOrderAddress, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderAddress.'` WHERE parent_id='.$orderId);
					}
					if (in_array($tblSalesOrderItem, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderItem.'` WHERE order_id='.$orderId);
					}
					if (in_array($tblSalesOrderPayment, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderPayment.'` WHERE parent_id='.$orderId);
					}
					if (in_array($tblSalesOrderStatusHistory, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderStatusHistory.'` WHERE parent_id='.$orderId);
					}
					if ($incId && in_array($tblSalesOrderGrid, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderGrid.'` WHERE increment_id='.$incId);
					}
					if (in_array($tblSalesOrderTax, $showTables)) {
						$connection->rawQuery('DELETE FROM `'.$tblSalesOrderTax.'` WHERE order_id='.$orderId);
					}
					if ($quoteId && $showTablesLog) {
						$connection->rawQuery('DELETE FROM `'.$tblLogQuote.'` WHERE quote_id='.$quoteId);
					}
					$connection->rawQuery('SET FOREIGN_KEY_CHECKS=1');
				}
				
				$this->messageManager->addSuccess(__('Successfully deleted 1 order.'));
		
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			}	
			
		} else {
			$this->messageManager->addError(__('Unable to delete this order.'));
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('sales/*/');
		return $resultRedirect;
		
	}
    
}
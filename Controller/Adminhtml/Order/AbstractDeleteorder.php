<?php
namespace Ecomteck\Deleteorder\Controller\Adminhtml\Order;

abstract class AbstractDeleteorder extends \Magento\Backend\App\Action
{
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('deleteorder');
    }
	
	

    
}
<?php
//
//
//namespace Chopserve\CustomShipping\Model;
//
//
//use Chopserve\CustomShipping\Api\ShippingDetailInterface;
//
//class OrderIdByInvoice implements ShippingDetailInterface
//{
//
//
//    public function getdetails(){
//        $order_id = intval($_POST['order_id']);
//        $order = Mage::getModel('sales/order')->load($order_id);
//        $invoice = $order->getInvoiceCollection()
//            ->addAttributeToSort('created_at', 'DSC')
//            ->setPage(1, 1)
//            ->getFirstItem();
//
//        return $invoice;
//
//
//
//    }
//}

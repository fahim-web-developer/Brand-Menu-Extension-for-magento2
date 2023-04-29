<?php
namespace Fahim\BrandMenu\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface; 
use Magiccart\Shopbrand\Block\ListBrand; 
use Magiccart\Shopbrand\Helper\Data;

class Index extends \Magento\Framework\View\Element\Template
{

  protected $_productloader;  

  public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResultFactory $resultFactory,
        StoreManagerInterface $storeManager,
        Data $helper,
        ListBrand $listBrand,
        \Magiccart\Shopbrand\Model\ShopbrandFactory $shopbrandFactory,
        array $data = []

    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultFactory = $resultFactory;
        $this->_storeManager = $storeManager;
        $this->_shopbrandFactory = $shopbrandFactory;
        $this->_helper = $helper;
        $this->_listBrand = $listBrand;
        parent::__construct($context, $data);
    }

     
    public function getBrands() {

         
        $store = $this->_storeManager->getStore()->getStoreId();
        $collection = $this->_shopbrandFactory->create()->getCollection()
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1);
 

        $productCount = $this->_helper->getConfigModule('list_page_settings/show_product_count');

        $brandTitle = array(); 
        foreach ($collection as $brand){ 

            $title = strtoupper($brand->getTitle());
            $href = $this->_helper->getLinkBrand($brand);
            $ProductCount = $this->_listBrand->getProductCount($brand);

            if(array_key_exists($title[0],$brandTitle)){
                 
                 $brandData = "<li><a href='".$brand->getUrlkey()."' class='text-decoration-none text-dark'>".$brand->getTitle()."</a></li>"; 

                 $brandTitle[$title[0]][] = $brandData;

            }else{

                $a = "<li><a href='".$brand->getUrlkey()."'  class='text-decoration-none text-dark'>".$brand->getTitle()."</a></li>";
                $brandTitle[$title[0]][] = $a;
            }
        }

              
        ksort($brandTitle);

        $AlbrandTitle = array();

        foreach($brandTitle as $x=>$x_value){
           $AlbrandTitle[$x] = $brandTitle[$x];
        }               
 
        return $AlbrandTitle;

    }
}
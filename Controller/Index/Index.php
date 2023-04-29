<?php

namespace Fahim\BrandMenu\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface; 
use Magiccart\Shopbrand\Block\ListBrand; 

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $resultFactory;
    protected $_storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResultFactory $resultFactory,
        StoreManagerInterface $storeManager,
        ListBrand $listBrand,
        \Magiccart\Shopbrand\Helper\Data $helper,
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

    public function execute()
    {

        $search = $this->getRequest()->getParam('search');

        $keyword = ucwords($search);


        $store = $this->_storeManager->getStore()->getStoreId();
        if ($keyword) {
            
            $collection = $this->_shopbrandFactory->create()->getCollection()
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1)
                        ->addFieldToFilter('title',['like'=>$keyword.'%'])->setOrder('title','ASC');
        }else{
            $collection = $this->_shopbrandFactory->create()->getCollection()
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1)->setOrder('title','ASC');
        }
        

        $productCount = $this->_helper->getConfigModule('list_page_settings/show_product_count');

        $brandTitle = array(); 
        foreach ($collection as $brand){ 

            $title = strtoupper($brand->getTitle());
            $href = $this->_helper->getLinkBrand($brand);
            $ProductCount = $this->_listBrand->getProductCount($brand);

            if(array_key_exists($title[0],$brandTitle)){
                $brandData = array("title" => $brand->getTitle(),"urlkey" => $brand->getUrlkey());

                 $brandTitle[$title[0]][] = $brandData;

            }else{
                $brandTitle[$title[0]][] =  array("title" => $brand->getTitle(),"urlkey" => $brand->getUrlkey());
            }
        }

              
        ksort($brandTitle);

        $AlbrandTitle = array();

        foreach($brandTitle as $x=>$x_value){
           $AlbrandTitle[$x] = $brandTitle[$x];
        }
               


        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($AlbrandTitle);
     
        return $resultJson;
    }
}


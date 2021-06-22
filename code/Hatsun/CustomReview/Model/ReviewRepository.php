<?php


namespace Hatsun\CustomReview\Model;

use Hatsun\CustomReview\Api\ReviewRepositoryInterface;



class ReviewRepository implements ReviewRepositoryInterface
{

    protected $productRepository; 
    protected $_reviewCollection; 
    protected $_ratingFactory;
    protected $voteCollection;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
	\Magento\Review\Model\RatingFactory $ratingFactory,
	\Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteCollection,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
    ) {
        $this->productRepository = $productRepository;
	$this->_ratingFactory = $ratingFactory;
	$this->_voteCollection= $voteCollection;
        $this->_reviewCollection = $reviewCollection;
    }
  
    public function getCollection($productId)
    {
      $product = $this->productRepository->getById($productId);
	$result = [];
      $collection = $this->_reviewCollection->create()->addFieldToSelect('*')
        ->addStatusFilter(
          \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
        'product',
          $productId
        )->setDateOrder()->addRateVotes();
      // $collection->getData();
      $i=0;
      if($collection != ''){
        foreach($collection as $review){
          $result[$i]['title'] = $review->getTitle();
          $result[$i]['detail'] = $review->getDetail();
          $result[$i]['nickname'] = $review->getNickname();
          $result[$i]['created_at'] = $review->getCreatedAt();
          $result[$i]['review_id'] = $review->getReviewId();
          $result[$i]['customer_id'] = $review->getCustomerId();
          $result[$i]['entity_id'] = $review->getEntityId();
          $result[$i]['entity_pk_value'] = $review->getEntityPkValue();
          $result[$i]['entity_code'] = $review->getEntityCode();
          $rating = $this->_voteCollection->create();
          $rating->addRatingInfo()->addOptionInfo()->addRatingOptions()->addFieldToFilter('review_id',$review->getReviewId());
          $result[$i++]['rating'] = $rating->getData();
        }
      }else {
        $result['msg'] = "empty";
      }
      echo json_encode($result);
      die();
    }

    public function postReviews($params)
    {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $customer_id = $params['customer_id'];
      $ratings = $params['ratings'];
      
      if($customer_id != ''){
        $_review = $objectManager->get("Magento\Review\Model\Review")
        ->setEntityPkValue($params['product_id'])    //product Id
        ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)// pending/approved
        ->setEntityId(1)
        // ->setStoreId(1)
        ->setStores(1)
        ->setTitle($params['review_title'])
        ->setDetail($params['review_detail'])
        ->setNickname($params['nickname'])
        ->setCustomerId($customer_id);
        if($_review->save()){
          $result['msg1'] = "Reviews have been submitted!";
        }else {
          $result['msg1'] = "Failed to save";
        }
        foreach ($ratings as $ratingId => $optionId) {
          $this->_ratingFactory->create()
              ->setRatingId($ratingId)
              ->setReviewId($_review->getId())
              ->addOptionVote($optionId, $params['product_id']);
        }
        if($_review->aggregate()){
          $result['msg2'] = "Ratings added!";
        }else {
          $result['msg2'] = "Failed to add";
        }

      }else {
        $result['msg'] = "Fail";
      }
      
      
      echo json_encode($result);
      die();
    }
}
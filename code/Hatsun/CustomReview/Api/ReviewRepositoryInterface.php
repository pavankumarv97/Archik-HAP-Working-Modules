<?php

namespace Hatsun\CustomReview\Api;

/**
 * Interface ReviewRepositoryInterface
 * @api
 */
interface ReviewRepositoryInterface
{

    /**
     * Review List.
     *
     * @param int $product_id
     * @return array
     */
    public function getCollection($productId);

    /**
     * Review Post.
     *
     * @param mixed $params
     * @return array
     */

    public function postReviews($params);



}
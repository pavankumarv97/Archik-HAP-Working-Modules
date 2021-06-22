<?php
namespace Chopserve\SourceMapping\Model\Mapping\Executor;

use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Api\ExecutorInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var MappingRepositoryInterface
     */
    private $mappingRepository;

    /**
     * Delete constructor.
     * @param MappingRepositoryInterface $mappingRepository
     */
    public function __construct(
        MappingRepositoryInterface $mappingRepository
    ) {
        $this->mappingRepository = $mappingRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->mappingRepository->deleteById($id);
    }
}

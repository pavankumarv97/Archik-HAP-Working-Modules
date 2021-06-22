<?php
namespace Chopserve\SourceMapping\Test\Unit\Model\Mapping\Executor;

use PHPUnit\Framework\TestCase;
use Chopserve\SourceMapping\Api\MappingRepositoryInterface;
use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Model\Mapping\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Chopserve\SourceMapping\Model\Mapping\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var MappingRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $mappingRepository */
        $mappingRepository = $this->createMock(MappingRepositoryInterface::class);
        $mappingRepository->expects($this->once())->method('deleteById');
        /** @var MappingInterface | \PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this->createMock(MappingInterface::class);
        $delete = new Delete($mappingRepository);
        $delete->execute($mapping->getId());
    }
}

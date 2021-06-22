<?php
namespace Chopserve\SourceMapping\Test\Unit\Block\Adminhtml\Button\Mapping;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Chopserve\SourceMapping\Api\Data\MappingInterface;
use Chopserve\SourceMapping\Block\Adminhtml\Button\Mapping\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var UrlInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $url;
    /**
     * @var Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;
    /**
     * @var Delete
     */
    private $button;

    /**
     * set up tests
     */
    protected function setUp()
    {
        $this->url = $this->createMock(UrlInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->button = new Delete($this->registry, $this->url);
    }

    /**
     * @covers \Chopserve\SourceMapping\Block\Adminhtml\Button\Mapping\Delete::getButtonData()
     */
    public function testButtonDataNoMapping()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Chopserve\SourceMapping\Block\Adminhtml\Button\Mapping\Delete::getButtonData()
     */
    public function testButtonDataNoMappingId()
    {
        $mapping = $this->createMock(MappingInterface::class);
        $mapping->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($mapping);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Chopserve\SourceMapping\Block\Adminhtml\Button\Mapping\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $mapping = $this->createMock(MappingInterface::class);
        $mapping->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($mapping);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}

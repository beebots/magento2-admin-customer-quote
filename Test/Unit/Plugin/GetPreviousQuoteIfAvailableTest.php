<?php


namespace BeeBots\AdminCustomerQuote\Test\Unit\Plugin;


use BeeBots\AdminCustomerQuote\Model\ResourceModel\CustomerQuoteResource;
use BeeBots\AdminCustomerQuote\Plugin\GetPreviousQuoteIfAvailable;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class GetPreviousQuoteIfAvailableTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var GetPreviousQuoteIfAvailable */
    private $getPreviousQuoteIfAvailable;

    /** @var Quote|Mockery\|MockInterface */
    private $quoteSubjectMock;

    /** @var CustomerQuoteResource|MockInterface */
    private $customerQuoteResourceMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->quoteSubjectMock = Mockery::mock(Quote::class);
        $this->customerQuoteResourceMock = Mockery::mock(CustomerQuoteResource::class);
        $this->getPreviousQuoteIfAvailable = $this->objectManager->getObject(
            GetPreviousQuoteIfAvailable::class,
            [
                'customerQuoteResource' => $this->customerQuoteResourceMock,
            ]
        );
    }

    public function testSetSessionQuoteIfPreviousQuoteAvailableForCustomer()
    {
        $this->quoteSubjectMock->shouldReceive('getQuoteId')
            ->andReturn(null);
        $this->quoteSubjectMock->shouldReceive('getCustomerId')
            ->andReturn(101011);
        $this->customerQuoteResourceMock->shouldReceive('getLatestQuoteIdOrNullForCustomer')
            ->andReturn(101012);
        $this->quoteSubjectMock->shouldReceive('setQuoteId')
            ->withArgs([101012])
            ->once();
        $this->getPreviousQuoteIfAvailable->beforeGetQuote($this->quoteSubjectMock);
    }

    public function testDoNothingWhenPreviousQuoteNotAvailable()
    {
        $this->quoteSubjectMock->shouldReceive('getQuoteId')
            ->andReturn(null);
        $this->quoteSubjectMock->shouldReceive('getCustomerId')
            ->andReturn(101011);
        $this->customerQuoteResourceMock->shouldReceive('getLatestQuoteIdOrNullForCustomer')
            ->andReturn(null);
        $this->quoteSubjectMock->shouldNotReceive('setQuoteId');
        $this->getPreviousQuoteIfAvailable->beforeGetQuote($this->quoteSubjectMock);
    }

//    public function testDoNothingIfSessionObjectAlreadyHasQuoteId()
//    {
//        $this->quoteSubjectMock->shouldReceive('getQuoteId')
//            ->andReturn(101011);
//        $this->quoteSubjectMock->shouldReceive('getCustomerId')
//            ->andReturn(101011);
//        $this->customerQuoteResourceMock->shouldNotReceive('getLatestQuoteIdOrNullForCustomer');
//        $this->quoteSubjectMock->shouldNotReceive('setQuoteId');
//        $this->getPreviousQuoteIfAvailable->beforeGetQuote($this->quoteSubjectMock);
//    }

    public function testDoNothingIfNoCustomerId()
    {
        $this->quoteSubjectMock->shouldReceive('getQuoteId')
            ->andReturn(null);
        $this->quoteSubjectMock->shouldReceive('getCustomerId')
            ->andReturn(null);
        $this->customerQuoteResourceMock->shouldNotReceive('getLatestQuoteIdOrNullForCustomer');
        $this->quoteSubjectMock->shouldNotReceive('setQuoteId');
        $this->getPreviousQuoteIfAvailable->beforeGetQuote($this->quoteSubjectMock);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}

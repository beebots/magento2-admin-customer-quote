<?php

namespace BeeBots\AdminCustomerQuote\Test\Unit\Plugin;

use BeeBots\AdminCustomerQuote\Plugin\DeleteCanceledQuote;
use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Controller\Adminhtml\Order\Create\Cancel;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class DeleteCanceledQuoteTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var DeleteCanceledQuote */
    private $deleteCanceledQuote;

    /** @var RequestInterface|MockInterface */
    private $requestMock;

    /** @var QuoteRepository|MockInterface */
    private $quoteRepositoryMock;

    /** @var QuoteSession|MockInterface */
    private $quoteSessionMock;

    /** @var Cancel|MockInterface */
    private $cancelMock;

    /** @var int */
    private $callbackCalled;

    /** @var Quote|MockInterface */
    private $quoteMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->requestMock = Mockery::mock(RequestInterface::class);
        $this->quoteRepositoryMock = Mockery::mock(QuoteRepository::class);
        $this->quoteSessionMock = Mockery::mock(QuoteSession::class);
        $this->quoteMock = Mockery::mock(Quote::class);
        $this->cancelMock = Mockery::mock(Cancel::class);
        $this->deleteCanceledQuote = $this->objectManager->getObject(
            DeleteCanceledQuote::class,
            [
                'quoteSession' => $this->quoteSessionMock,
                'quoteRepository' => $this->quoteRepositoryMock,
                'request' => $this->requestMock,
            ]
        );
        $this->callbackCalled = 0;
    }

    public function testQuoteIsDeletedWhenCancelIsCalled()
    {
        $this->requestMock->shouldReceive('getParam')
            ->withArgs(['delete', true])
            ->andReturn(null);
        $this->quoteSessionMock->shouldReceive('getQuote')
            ->andReturn($this->quoteMock);
        $this->quoteMock->shouldReceive('getIsActive')
            ->andReturn(false);
        $this->quoteRepositoryMock->shouldReceive('delete')
            ->withArgs([$this->quoteMock])
            ->once();
        $returnValue = $this->deleteCanceledQuote->aroundExecute(
            $this->cancelMock,
            function () {
                $this->callbackCalled++;
                return 'returnValue';
            }
        );
        $this->assertTrue($this->callbackCalled === 1);
        $this->assertTrue($returnValue === 'returnValue');
    }

    public function testQuoteIsNotDeletedWhenCancelIsCalledWhenQuoteIsActive()
    {
        $this->requestMock->shouldReceive('getParam')
            ->withArgs(['delete', true])
            ->andReturn(null);
        $this->quoteSessionMock->shouldReceive('getQuote')
            ->andReturn($this->quoteMock);
        $this->quoteMock->shouldReceive('getIsActive')
            ->andReturn(true);
        $this->quoteRepositoryMock->shouldNotReceive('delete');
        $returnValue = $this->deleteCanceledQuote->aroundExecute(
            $this->cancelMock,
            function () {
                $this->callbackCalled++;
                return 'returnValue';
            }
        );
        $this->assertTrue($this->callbackCalled === 1);
        $this->assertTrue($returnValue === 'returnValue');
    }

    public function testQuoteIsNotDeletedWhenCancelIsCalledWithDeleteFalseQueryParam()
    {
        $this->requestMock->shouldReceive('getParam')
            ->withArgs(['delete', true])
            ->andReturn('false');
        $this->quoteSessionMock->shouldNotReceive('getQuote');
        $this->quoteRepositoryMock->shouldNotReceive('delete');
        $returnValue = $this->deleteCanceledQuote->aroundExecute(
            $this->cancelMock,
            function () {
                $this->callbackCalled++;
                return 'returnValue';
            }
        );
        $this->assertTrue($this->callbackCalled === 1);
        $this->assertTrue($returnValue === 'returnValue');
    }

    public function tearDown()
    {
        Mockery::close();
    }
}

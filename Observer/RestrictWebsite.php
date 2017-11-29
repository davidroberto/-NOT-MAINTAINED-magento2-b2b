<?php
namespace DavidRobert\B2B\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\UrlInterface;
use \Magento\Customer\Model\Session;
use \Magento\Framework\App\Response\Http as Response;
use \Magento\Framework\Message\ManagerInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\State;
use \Magento\Framework\App\Area;
use \DavidRobert\B2B\Helper\Data;

class RestrictWebsite implements ObserverInterface
{

	/**
	 * @var UrlInterface
	 */
	protected $_urlInterface;

	/**
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var Session
	 */
	protected $_customerSession;

	/**
	 * @var Response
	 */
	protected $_response;


	/**
	 * @var ManagerInterface
	 */
	protected $_messageManager;

	/**
	 * @var State
	 */
	protected $_state;

	/**
	 * @var Data
	 */
	protected $_dataHelper;

	/**
	 * @param \Magento\Framework\UrlInterface $urlInterface
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\App\Response\Http $response
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\App\State $state
	 * @Param \DavidRobert\RestrictPage\Helper\Data $dataHelper
	 */
	public function __construct(
		UrlInterface $urlInterface,
		StoreManagerInterface $storeManager,
		Session $customerSession,
		Response $response,
		ManagerInterface $messageManager,
		State $state,
		Data $dataHelper
	) {
		$this->_urlInterface = $urlInterface;
		$this->_storeManager = $storeManager;
		$this->_customerSession = $customerSession;
		$this->_response = $response;
		$this->_messageManager = $messageManager;
		$this->_state = $state;
		$this->_dataHelper = $dataHelper;
	}


	/**
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 */
	public function execute(Observer $observer)
	{

		$websiteB2B = $this->getWebsiteB2B();

		// check if area id front end
		if ($this->_state->getAreaCode() === Area::AREA_FRONTEND) {

			// check if the current website id is the website to restrict
			if ($this->getWebsiteId() === $websiteB2B) {

				$this->redirectCustomer();

			}
		}

	}


	/**
	 * Redirect customer depending on loggin
	 *
	 * @return void
	 */
	public function RedirectCustomer() {

		$currentCustomerGroup = $this->_customerSession->getCustomer()->getGroupId();
		$customerGroupB2B = $this->getCustomerGroupB2B();

		if ( ! $this->_customerSession->isLoggedIn() ) {

			$this->redirectToLoginPage();

		} else if ( $currentCustomerGroup !== $customerGroupB2B ) {

			$this->redirectToRootStore();

		}

	}


	/**
	 * Redirect to login page
	 *
	 * @return void
	 */
	public function redirectToLoginPage()
	{

		$loginUrl = $this->_urlInterface->getUrl(
			'customer/account/login'
		);
		$currentUrl = $this->_urlInterface->getCurrentUrl();

		if ($loginUrl !== $currentUrl) {

			$messageToConnect = 'Veuillez vous connecter à votre compte client B2B pour voir ce magasin';
			// add a message to the flashbag message
			$this->_messageManager->addWarning(__($messageToConnect));
			// redirect to the login url
			$this->_response->setRedirect($loginUrl);
		}
	}

	/**
	 * Redirect to default store view
	 *
	 * @return void
	 */
	public function redirectToRootStore()
	{
		$messageWrongGroup = 'Vous n\'avez pas accès à ce magasin';

		$this->_messageManager->addWarning(__($messageWrongGroup));
		$this->_response->setRedirect($this->_storeManager->getDefaultStoreView()->getBaseUrl());
	}


	/**
	 * Get website identifier
	 *
	 * @return string|int|null
	 */
	public function getWebsiteId()
	{
		return $this->_storeManager->getStore()->getWebsiteId();
	}


	/**
	 *
	 * @return int
	 */
	public function getWebsiteB2B()
	{
		return $this->_dataHelper->getGeneralConfig('website_b2b');
	}

	/**
	 *
	 * @return int
	 */
	public function getCustomerGroupB2B()
	{
		return $this->_dataHelper->getGeneralConfig('group_b2b');
	}
}
<?php
namespace NoCaptcha;

use ReCaptcha\ReCaptcha;
use Zend\Captcha\AbstractAdapter;


/**
 * Class NoCaptchaAdapter
 *
 * @package NoCaptcha
 * @author Adam Balint <adam.balint@srg.hu>
 */
class NoCaptchaAdapter extends AbstractAdapter
{

	/**
	 * @var ReCaptcha
	 */
	protected $service;

	/**
	 * @var string
	 */
	protected $siteKey;

	/**
	 * @var string
	 */
	protected $secretKey;

	/**
	 * light | dark
	 *
	 * @var string
	 */
	protected $theme = 'light';

	/**
	 * image | audio
	 *
	 * @var string
	 */
	protected $type = 'image';

	/**
	 * @var string
	 */
	protected $callback = 'recaptchaCallback';

	/**
	 * See the different options on https://developers.google.com/recaptcha/docs/display
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Error codes
	 */
	const MISSING_VALUE = 'missingValue';
	const ERR_CAPTCHA   = 'errCaptcha';

	/**
	 * Error messages
	 * @var array
	 */
	protected $messageTemplates = array(
		self::MISSING_VALUE => 'Missing captcha fields',
		self::ERR_CAPTCHA   => 'Failed to validate captcha',
	);


	/**
	 * @param null $options
	 */
	public function __construct($options = null)
	{

		parent::__construct($options);

		if (!empty($options)) {
			if (array_key_exists('siteKey', $options)) {
				$this->setSiteKey($options['siteKey']);
			}
			if (array_key_exists('secretKey', $options)) {
				$this->setSecretKey($options['secretKey']);
			}
			$this->setOptions($options);
		}

		$this->setService(new ReCaptcha($this->getSecretKey()));

	}

	/**
	 * @param $siteKey
	 *
	 * @return $this
	 */
	public function setSiteKey($siteKey)
	{
		$this->siteKey=$siteKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSiteKey()
	{
		return $this->siteKey;
	}

	/**
	 * @param $secretKey
	 *
	 * @return $this
	 */
	public function setSecretKey($secretKey)
	{
		$this->secretKey=$secretKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSecretKey()
	{
		return $this->secretKey;
	}

	/**
	 * @return ReCaptcha
	 */
	public function getService()
	{
		return $this->service;
	}


	/**
	 * @param ReCaptcha $service
	 *
	 * @return ReCaptcha
	 */
	public function setService(ReCaptcha $service)
	{
		return $this->service = $service;
	}

	/**
	 * @return string
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param string $callback
	 *
	 * @return $this
	 */
	public function setCallback($callback)
	{
		$this->callback = $callback;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 * @param string $theme
	 *
	 * @return $this
	 */
	public function setTheme($theme)
	{
		$this->theme = $theme;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}


	/**
	 * @return string
	 */
	public function generate()
	{
		return "";
	}


	/**
	 * Check if captcha is valid
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function isValid($value)
	{
		if (!$value) {
			$this->error(self::MISSING_VALUE);
			return false;
		}

		$response = $this->getService()->verify($value);

		if ($response->isSuccess() === true) {
			return true;
		}

		$this->error(self::ERR_CAPTCHA);
		return false;
	}

	/**
	 * Get helper name
	 *
	 * @return string
	 */
	public function getHelperName()
	{
		return 'recaptcha.helper';
	}

}
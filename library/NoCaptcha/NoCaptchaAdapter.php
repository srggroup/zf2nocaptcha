<?php

declare(strict_types=1);

namespace NoCaptcha;

use Exception;
use Laminas\Captcha\AbstractAdapter;
use ReCaptcha\ReCaptcha;

class NoCaptchaAdapter extends AbstractAdapter {


	/**
	 * Error codes
	 */
	public const string MISSING_VALUE = 'missingValue';
	public const string ERR_CAPTCHA   = 'errCaptcha';


	protected ReCaptcha $service;

	protected string $siteKey;

	protected string $secretKey;

	/**
	 * light | dark
	 */
	protected string $theme = 'light';

	/**
	 * image | audio
	 */
	protected string $type = 'image';

	protected string $callback = 'recaptchaCallback';

	/** @var bool Invisible ReCaptcha, or v2 */
	protected bool $invisible = false;

	/**
	 * See the different options on https://developers.google.com/recaptcha/docs/display
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Error messages
	 *
	 * @var array
	 */
	protected $messageTemplates = [
		self::MISSING_VALUE => 'Missing captcha fields',
		self::ERR_CAPTCHA   => 'Failed to validate captcha',
	];


	/**
	 * @param null $options
	 */
	public function __construct($options = null) {

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


	public function setSiteKey(string $siteKey): static {
		$this->siteKey = $siteKey;

		return $this;
	}


	public function getSiteKey(): string {
		return $this->siteKey;
	}


	public function setSecretKey(string $secretKey): static {
		$this->secretKey = $secretKey;

		return $this;
	}


	public function getSecretKey(): string {
		return $this->secretKey;
	}


	public function getService(): ReCaptcha {
		return $this->service;
	}


	public function setService(ReCaptcha $service): ReCaptcha {
		return $this->service = $service;
	}


	public function getCallback(): string {
		return $this->callback;
	}


	public function setCallback(string $callback): static {
		$this->callback = $callback;

		return $this;
	}


	public function getTheme(): string {
		return $this->theme;
	}


	public function setTheme(string $theme): static {
		$this->theme = $theme;

		return $this;
	}


	public function getType(): string {
		return $this->type;
	}


	public function setType(string $type): static {
		$this->type = $type;

		return $this;
	}


	public function generate(): string {
		return "";
	}


	/**
	 * Check if captcha is valid
	 *
	 * @param mixed $value
	 * @throws Exception
	 */
	public function isValid($value): bool {
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


	public function getHelperName(): string {
		return 'recaptcha.helper';
	}


	public function isInvisible(): bool {
		return $this->invisible;
	}


	public function setInvisible(bool $invisible): void {
		$this->invisible = $invisible;
	}


}

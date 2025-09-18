<?php

namespace NoCaptcha;

use Laminas\Captcha\AdapterInterface;
use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;


class NoCaptchaHelper extends FormInput {


	/**
	 * @return $this|string|FormInput
	 */
	public function __invoke(?ElementInterface $element = null) {
		if (!$element) {
			return $this;
		}

		return $this->render($element);
	}


	public function render(ElementInterface $element): string {
		$captcha = $element->getCaptcha();

		if ($captcha === null || !$captcha instanceof AdapterInterface) {
			throw new Exception\DomainException(sprintf(
				'%s requires that the element has a "captcha" attribute implementing Laminas\Captcha\AdapterInterface',
				__METHOD__
			));
		}

		$name = $element->getName();
		$id = $element->getAttribute('id') ?: $name;
		$id .= '_' . uniqid(); //Generate unique ID for field

		$captchaPattern = '<div %s></div>';

		$captchaAttributes = [
			'class'         => 'g-recaptcha ' . $element->getAttribute('class'),
			'data-sitekey'  => $captcha->getSiteKey(),
			'data-theme'    => $captcha->getTheme(),
			'data-type'     => $captcha->getType(),
			'data-callback' => $captcha->getCallback(),
			'id'            => $id,
		];

		//Invisible recaptcha support
		if ($captcha->isInvisible()) {
			$captchaAttributes['data-size'] = 'invisible';
		}
		$captchaAttributes = $this->createAttributesString($captchaAttributes);


		$captchaElement = sprintf($captchaPattern, $captchaAttributes);
		$input = $this->renderHiddenInput($id . '_input', $name);
		$js = $this->renderJsCallback($captcha->getCallback(), $id . '_input');

		return $captchaElement . $input . $js;
	}


	protected function renderHiddenInput(string $id, string $name): string {
		$pattern = '<input type="hidden" %s%s';
		$closingBracket = $this->getInlineClosingBracket();

		$attributes = $this->createAttributesString([
			'id'    => $id,
			'name'  => $name,
			'class' => 'recaptchaResponse',
		]);

		return sprintf($pattern, $attributes, $closingBracket);
	}


	protected function renderJsCallback(string $callback, string $id): string {
		$lang = LANG;
		$js = '';
		$js .= <<<SCRIPT
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl={$lang}"></script>
SCRIPT;

		$js .= <<<SCRIPT
<script type="text/javascript" language="JavaScript">
var {$callback} = function(response) {
        document.getElementById('{$id}').value = response;
      };

</script>
SCRIPT;

		return $js;

	}


}
# ZF2NoCaptcha

This ZendFramework2 Captcha adapter uses Google's reCAPTCHA php library (see here: https://github.com/google/recaptcha).

##USAGE

* Pull it from composer
```
composer require srggroup/zf2nocaptcha
```
* Define the helper in your zend module config: 
```
'view_helpers'=>array(
	'invokables'=>array(
		'recaptcha.helper'=>'NoCaptcha\NoCaptchaHelper'
	)
)
```
* Integrate it into the form like the standard zend recaptcha element
```
$adapter = new \NoCaptcha\NoCaptchaAdapter($siteKey, $secreteKey);
```


I've used some code from this repository: https://github.com/szmnmichalowski/ZF2-NoCaptcha

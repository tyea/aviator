<?php

use Symfony\Component\HttpFoundation\Request as RequestFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validation as ValidatorFactory;
use Tyea\Aviator\Constraints\CollectionFactory;
use Symfony\Component\PropertyAccess\PropertyAccess as PropertyAccessorFactory;
use Tyea\Aviator\ResponseFactory;
use Symfony\Component\HttpClient\CurlHttpClient as Curl;

function env(string $key, $default = null)
{
	return $_ENV[$key] ?? $default;
}

function request(): Request
{
	return RequestFactory::createFromGlobals();

}
function session(): Session
{
	return new Session();
}

function validate(array $data, array $rules, bool $missing = false, bool $extra = false): array
{
	$validator = ValidatorFactory::createValidator();
	$collection = CollectionFactory::createFromArray($rules, $missing, $extra);
	$violations = $validator->validate($data, $collection);
	$errors = [];
	if ($violations) {
		$propertyAccessor = PropertyAccessorFactory::createPropertyAccessor();
		foreach ($violations as $violation) {
			$propertyAccessor->setValue($errors, $violation->getPropertyPath(), $violation->getMessage());
		}
	}
	return $errors;
	
}

function response(): ResponseFactory
{
	return new ResponseFactory();
}

function dd($expression): void
{
	ob_start();
	var_dump($expression);
	$output = ob_get_clean();
	$content = "<pre>" . htmlspecialchars($output, ENT_COMPAT, "UTF-8") . "</pre>";
	response()->raw($content, 500);
}

function now(): DateTime
{
	return new DateTime("now", new DateTimeZone("UTC"));
}

function curl(array $options = []): Curl
{
	return new Curl($options);
}
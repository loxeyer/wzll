<?php
class SDK_Exception extends Exception
{
	public function __construct($message, array $variables = null, $code = 0)
	{
		if(!empty($variables))
			$message = strtr($message, $variables);

		parent::__construct($message, $code);
	}
}


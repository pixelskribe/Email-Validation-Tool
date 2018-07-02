<?php

declare(strict_types=1);

namespace EmailValidation;

use EmailValidation\Validations\EmailHostValidator;
use EmailValidation\Validations\RoleBasedEmailValidator;
use EmailValidation\Validations\DisposableEmailValidator;
use EmailValidation\Validations\FreeEmailServiceValidator;
use EmailValidation\Validations\MisspelledEmailValidator;
use EmailValidation\Validations\MxRecordsValidator;
use EmailValidation\Validations\Validator;
use EmailValidation\Validations\ValidFormatValidator;

class EmailValidatorFactory
{
    /** @var Validator[] */
    private static $defaultValidators = [
        ValidFormatValidator::class,        //0
        MxRecordsValidator::class,          //1
        MisspelledEmailValidator::class,    //2
        FreeEmailServiceValidator::class,   //3
        DisposableEmailValidator::class,    //4
        RoleBasedEmailValidator::class,     //5
        EmailHostValidator::class           //6
    ];

    /**
     * @param string $emailAddress
     * @param array $key of validators to use [default use all]
     * @return EmailValidator
     */
    public static function create(string $emailAddress, array $useValidators=[]): EmailValidator
    {
        $emailAddress = new EmailAddress($emailAddress);
        $emailDataProvider = new EmailDataProvider();
        $emailValidationResults = new ValidationResults();
        $emailValidator = new EmailValidator($emailAddress, $emailValidationResults, $emailDataProvider);

		//$validators = self::setValidators($useValidators);

        foreach (self::setValidators($useValidators) as $validator) {
            $emailValidator->registerValidator(new $validator);
        }

        return $emailValidator;
    }

    public static function setValidators($validators){
        $available = self::$defaultValidators;
        foreach($available as $key => $value){
            if(!in_array($key,$validators))
                unset($available[$key]);
        }
        return $available;
    }
}

<?php

namespace FvCommunityNews\Validator;
use Exception;

/**
 * ValidatorChain
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class ValidatorChain extends AbstractValidator
{
    /**
     * @var array
     */
    protected $validators = [];

    /**
     * __construct()
     *
     * @version 20120705
     * @param array $validators
     */
    public function __construct(array $validators=null)
    {
        if (null !== $validators) {
            $this->setValidators($validators);
        }
    }

    /**
     * setValidators()
     *
     * @version 20120705
     * @param array $validators
     * @return ValidatorChain
     */
    public function setValidators(array $validators)
    {
        $this->clearValidators();

        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    /**
     * clearValidators()
     *
     * @version 20120705
     * @return ValidatorChain
     */
    public function clearValidators()
    {
        $this->validators = [];
        return $this;
    }

    /**
     * getValidators()
     *
     * @version 20120705
     * @return array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * addValidator()
     *
     * @version 20120705
     * @param string|AbstractValidator $validator
     * @return ValidatorChain
     * @throws Exception
     */
    public function addValidator($validator)
    {
        if (is_string($validator) && class_exists($validator)) {
            $validator = new $validator();
        }

        if (is_object($validator) && $validator instanceof AbstractValidator) {
            $this->validators[ get_class($validator) ] = $validator;
        } else {
            throw new Exception('Invallid validator provided');
        }

        return $this;
    }

    /**
     * removeValidator()
     *
     * @version 20120705
     * @param string|AbstractValidator $validator
     * @return ValidatorChain
     */
    public function removeValidator($validator)
    {
        if (is_string($validator)) {
            unset($this->validators[ $validator ]);
        }

        if (is_object($validator) && $validator instanceof AbstractValidator) {
            unset($this->validators[ get_class($validator) ]);
        }

        return $this;
    }

    /**
     * validate()
     *
     * @version 20120705
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (null == $this->getValidators()) {
            return true;
        }

        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValid($value)) {
                $this->setMessage($validator->getMessage());
                return false;
            }
        }

        return true;
    }
}

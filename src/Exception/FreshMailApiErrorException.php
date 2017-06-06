<?php

namespace Preclowski\ReFreshMailer\Exception;

/**
 * {@inheritdoc}
 *
 * @author Konrad Pawlikowski <preclowski@gmail.com>
 */
class FreshMailApiErrorException extends \DomainException
{
    /** @var array */
    private $errors;

    /**
     * @param string $url
     * @param array $errors
     */
    public function __construct($url, $errors)
    {
        $this->errors = $errors;

        parent::__construct(sprintf('Error occured when executing API call to "%s"', $url));
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
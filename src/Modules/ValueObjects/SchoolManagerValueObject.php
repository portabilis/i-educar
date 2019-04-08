<?php

namespace iEducar\Modules\ValueObjects;

class SchoolManagerValueObject
{
    /**
     * @var integer
     */
    public $individualId;

    /**
     * @var integer
     */
    public $schoolId;

    /**
     * @var integer
     */
    public $roleId;

    /**
     * @var integer
     */
    public $accessCriteriaId;

    /**
     * @var string
     */
    public $accessCriteriaDescription;

    /**
     * @var integer
     */
    public $linkTypeId;

    /**
     * @var boolean
     */
    public $isChief;
}
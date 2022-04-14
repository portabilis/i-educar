<?php

namespace iEducar\Modules\ValueObjects;

class SchoolManagerValueObject
{
    /**
     * @var integer
     */
    public $employeeId;

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

    /**
     * @var integer
     */
    public $inepId;
}

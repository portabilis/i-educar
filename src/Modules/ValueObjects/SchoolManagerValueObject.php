<?php

namespace iEducar\Modules\ValueObjects;

class SchoolManagerValueObject
{
    /**
     * @var int
     */
    public $employeeId;

    /**
     * @var int
     */
    public $schoolId;

    /**
     * @var int
     */
    public $roleId;

    /**
     * @var int
     */
    public $accessCriteriaId;

    /**
     * @var string
     */
    public $accessCriteriaDescription;

    /**
     * @var int
     */
    public $linkTypeId;

    /**
     * @var bool
     */
    public $isChief;

    /**
     * @var int
     */
    public $inepId;
}

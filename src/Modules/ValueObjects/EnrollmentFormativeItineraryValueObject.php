<?php

namespace iEducar\Modules\ValueObjects;

class EnrollmentFormativeItineraryValueObject
{
    /**
     * @var integer
     */
    public $enrollmentId;

    /**
     * @var array
     */
    public $itineraryType;

    /**
     * @var array
     */
    public $itineraryComposition;

    /**
     * @var integer
     */
    public $itineraryCourse;

    /**
     * @var boolean
     */
    public $concomitantItinerary;
}

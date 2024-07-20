<?php

namespace App\Enums;


enum BatchingCriteriaEnum: string
{
    case SUBMISSION_DATE = 'submission_date';
    case ENCOUNTER_DATE = 'encounter_date';
}
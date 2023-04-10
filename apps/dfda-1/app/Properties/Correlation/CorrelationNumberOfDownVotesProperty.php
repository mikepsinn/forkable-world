<?php
/*
*  GNU General Public License v3.0
*  Contributors: ADD YOUR NAME HERE, Mike P. Sinn
 */

namespace App\Properties\Correlation;
use App\Models\Correlation;
use \App\Traits\PropertyTraits\CorrelationProperty;
use App\Properties\Base\BaseNumberOfDownVotesProperty;
class CorrelationNumberOfDownVotesProperty extends BaseNumberOfDownVotesProperty
{
    use CorrelationProperty;
    public $table = Correlation::TABLE;
    public $parentClass = Correlation::class;
}
<?php
/*
*  GNU General Public License v3.0
*  Contributors: ADD YOUR NAME HERE, Mike P. Sinn
 */

namespace App\Properties\UserVariable;
use App\Models\Correlation;
use App\Models\UserVariable;
use App\Traits\PropertyTraits\IsCalculated;
use App\Traits\PropertyTraits\UserVariableProperty;
use App\Properties\Base\BaseOptimalValueMessageProperty;
use App\Variables\QMUserVariable;
class UserVariableOptimalValueMessageProperty extends BaseOptimalValueMessageProperty
{
    use UserVariableProperty;
    use IsCalculated;
    public $table = UserVariable::TABLE;
    public $parentClass = UserVariable::class;
    /**
     * @param QMUserVariable|UserVariable $uv
     * @return string
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public static function calculate($uv): ?string{
        $msg = self::getNotEnoughDataMessage($uv);
        $c = $uv->setBestUserCorrelation();
        $l = $uv->l();
		$dbm = $uv->getDBModel();
	    $dbm->setBestStudyLink();
        if($c){
			$msg = $c->generatePredictorExplanationSentence()." ";
	        $msg .= $c->changeFromBaselineSentence();
            $l->best_user_correlation_id = $c->getId();
            if($dbm->userBestCauseVariableId){$l->best_cause_variable_id = $dbm->userBestCauseVariableId;}
            if($dbm->userBestEffectVariableId){$l->best_effect_variable_id = $dbm->userBestEffectVariableId;}
	        $dbm->logInfo($msg);
        }
        if(!$l->best_user_correlation_id){
            if($dbm->isOutcome()){
                if($correlationsAsEffect = $dbm->calculateNumberOfUserCorrelationsAsEffect()){
	                $dbm->setBestUserCorrelation();
	                $dbm->throwLogicException("No BEST_USER_CORRELATION_ID even though we have $correlationsAsEffect correlations as a effect! ".
                        Correlation::getDataLabIndexUrl([Correlation::FIELD_EFFECT_USER_VARIABLE_ID => $dbm->getUserVariableId()]));
                }
            }else{
                if($correlationsAsCause = $dbm->calculateNumberOfUserCorrelationsAsCause()){
	                $dbm->setBestUserCorrelation();
	                $dbm->throwLogicException("No BEST_USER_CORRELATION_ID even though we have $correlationsAsCause correlations as a cause!".
                        Correlation::getDataLabIndexUrl([Correlation::FIELD_CAUSE_USER_VARIABLE_ID => $dbm->getVariableIdAttribute()]));
                }
            }
        }
	    $dbm->setAttribute(static::NAME, $msg);
        return $msg;
    }
	/**
	 * @param \App\Variables\QMUserVariable|UserVariable $uv
	 * @return string
	 */
	public static function getNotEnoughDataMessage($uv): string{
		return "We still don't have enough data to determine the optimal daily values for " . $uv->getVariableName() .
			". ";
	}
}
<?php

namespace App\Service;

use App\Controller\ProjectController;
use App\Entity\Project;
use App\Entity\ProjectFeature;

class ProjectCalculator
{
    const EXPERT_SPEED_COEFFICIENT=1;
    const CONFIRMED_SPEED_COEFFICIENT=1.5;
    const JUNIOR_SPEED_COEFFICIENT=2;

    public function calculateProjectLoad(Project $project, $projectFeatures) : float
    {
        //calculate project team mean velocity
        $velocity=$project->getExpert()/100 * self::EXPERT_SPEED_COEFFICIENT
            + $project->getConfirmed()/100 * self::CONFIRMED_SPEED_COEFFICIENT
            + $project->getJunior()/100 * self::JUNIOR_SPEED_COEFFICIENT;

        //get theoretical (expert based) project load
        $theoreticalLoad=0;
        foreach ($projectFeatures as $projectFeature) {
            $theoreticalLoad+=$projectFeature->getDay();
        }

        //return
        return round($theoreticalLoad * $velocity, 2);
    }

    /**
     * Check is he project feature is used by at least one variant in its related project
     * Return true if it is used.
     * Return false if no project variant uses the projectFeature.
     * @param ProjectFeature $projectFeature
     * @return bool
     */
    public function isActive(ProjectFeature $projectFeature)
    {
        foreach (ProjectController::VARIANTS as $variant) {
            if ($projectFeature->{'getIs'.$variant}()) {
                return true;
            }
        }

        return false;
    }
}

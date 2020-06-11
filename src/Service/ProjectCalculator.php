<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProjectCalculator
{
    const EXPERT_SPEED_COEFFICIENT=1;
    const CONFIRMED_SPEED_COEFFICIENT=1.5;
    const JUNIOR_SPEED_COEFFICIENT=2;

    public function calculateProjectLoad(Project $project) : float
    {

        //calculate project team mean velocity
        $velocity=$project->getExpert()/100 * self::EXPERT_SPEED_COEFFICIENT
            + $project->getConfirmed()/100 * self::CONFIRMED_SPEED_COEFFICIENT
            + $project->getJunior()/100 * self::JUNIOR_SPEED_COEFFICIENT;

        //get theoretical (expert based) project load
        $theoreticalLoad=0;
        $features=$project->getProjectFeatures();
        foreach ($features as $feature) {
            $theoreticalLoad+=$feature->getDay();
        }

        //return
        return round($theoreticalLoad * $velocity, 2);
    }

    public function getCategories(Project $project) : array
    {
        $featureCategories= [];
        $projectFeatures=$project->getProjectFeatures();
        foreach ($projectFeatures as $projectFeature) {
            $category=$projectFeature->getCategory();
            //
            if (!in_array($category, $featureCategories)) {
                $featureCategories[]=$category;
            }
        }

        return $featureCategories;
    }
}

<?php

namespace App\Service;

use App\Controller\ProjectController;
use App\Entity\Project;
use App\Entity\ProjectFeature;
use App\Repository\ProjectFeatureRepository;
use App\Repository\ProjectRepository;
use App\Repository\QuotationRepository;

class ProjectCalculator
{
    const EXPERT_SPEED_COEFFICIENT = 1;
    const CONFIRMED_SPEED_COEFFICIENT = 1.5;
    const JUNIOR_SPEED_COEFFICIENT = 2;

    /**
     * @var QuotationRepository
     */
    private $quotationRepository;
    private $projectRepository;
    private $projectFeatureRepos;

    public function __construct(
        QuotationRepository $quotationRepository,
        ProjectRepository $projectRepository,
        ProjectFeatureRepository $projectFeatureRepos
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->projectRepository = $projectRepository;
        $this->projectFeatureRepos = $projectFeatureRepos;
    }

    public function calculateProjectLoad(Project $project, $projectFeatures): float
    {
        //calculate project team mean velocity
        $velocity = $project->getExpert() / 100 * self::EXPERT_SPEED_COEFFICIENT
            + $project->getConfirmed() / 100 * self::CONFIRMED_SPEED_COEFFICIENT
            + $project->getJunior() / 100 * self::JUNIOR_SPEED_COEFFICIENT;

        //get theoretical (expert based) project load
        $theoreticalLoad = 0;
        foreach ($projectFeatures as $projectFeature) {
            $theoreticalLoad += $projectFeature->getDay();
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
        $variants = $this->quotationRepository->findAll();
        foreach ($variants as $variant) {
            if ($projectFeature->{'getIs' . $variant->getName()}()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the project list évaluation
     * read result[$projectId]['load'][$variant] to get the load for the given variant of the given project
     * read result[$projectId]['cost'][$variant] to get the cost for the given variant of the given project
     * @return array
     */
    public function calculateProjectsFigures() : array
    {
        $projects = $this->projectRepository->findAll();
        $prices = [];

        foreach ($projects as $project) {
            $highFeatures = $this->projectFeatureRepos->findProjectFeatures($project, 'high');
            $middleFeatures = $this->projectFeatureRepos->findProjectFeatures($project, 'middle');
            $lowFeatures = $this->projectFeatureRepos->findProjectFeatures($project, 'low');

            $prices[$project->getId()]['load']['high'] = number_format(
                $this->calculateProjectLoad($project, $highFeatures),
                2
            );

            $prices[$project->getId()]['load']['middle'] = number_format(
                $this->calculateProjectLoad($project, $middleFeatures),
                2
            );

            $prices[$project->getId()]['load']['low'] = number_format(
                $this->calculateProjectLoad($project, $lowFeatures),
                2
            );

            $prices[$project->getId()]['cost']['high'] = number_format(
                ProjectController::PRICE_PER_DAY * $this->calculateProjectLoad($project, $highFeatures),
                2
            );

            $prices[$project->getId()]['cost']['middle'] = number_format(
                ProjectController::PRICE_PER_DAY * $this->calculateProjectLoad($project, $middleFeatures),
                2
            );

            $prices[$project->getId()]['cost']['low'] = number_format(
                ProjectController::PRICE_PER_DAY * $this->calculateProjectLoad($project, $lowFeatures),
                2
            );
        }

        return $prices;
    }
}

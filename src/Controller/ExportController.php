<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectFeatureRepository;
use App\Repository\ProjectRepository;
use App\Repository\QuotationRepository;
use App\Service\ProjectCalculator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/{id}/export/{variant<high|middle|low>}", name="export")
     * @param Project $project
     * @param ProjectCalculator $projectCalculator
     * @param string $variant
     * @return Response
     */
    public function index(
        Project $project,
        ProjectCalculator $projectCalculator,
        string $variant = 'high'
    ): Response {


        $projectSynthesis = $projectCalculator->getProjectSynthesis($project);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('export/export.html.twig', [
            'project' => $project,
            'costs' => $projectCalculator->calculateProjectsFigures(),
            'variant' => $variant,
            'projectSynthesis' => $projectSynthesis,


        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("projet.pdf", [
            "Attachment" => false
        ]);
    }
}

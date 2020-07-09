<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Entity\Project;
use App\Service\ProjectCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends AbstractController
{
    /**
     * @Route("/{id}/export", name="export")
     * @param Project $project
     */
    public function index(Project $project, ProjectCalculator $projectCalculator)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('export/export.html.twig', [
            'project' => $project,
            'costs' => $projectCalculator->calculateProjectsFigures(),
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

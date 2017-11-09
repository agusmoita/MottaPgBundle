<?php

namespace MottaPgBundle\Util\Export;

use Symfony\Component\HttpFoundation\Response;

/**
* PdfExport
*/
class PdfExport extends Export
{
    /**
     * @var string
     */
	public $title;

    /**
     * @var TCPDF
     */
	private $tcpdf;

    /**
     * @param TCPDF $tcpdf
     * @return PdfExport
     */
    public function __construct($tcpdf)
    {
        $this->tcpdf = $tcpdf->create();
        $this->type = 'pdf';

        return $this;
    }

    /**
     * @param int $i
     * @param string $headerCall
     * @param string $rowCall
     * @param string $fileName
     * @param string $title
     * @param string $tooltip
     * @return PdfExport
     */
    public function create($i, $headerCall, $rowCall, $fileName, $title, $tooltip)
    {
        parent::init($i, $headerCall, $rowCall, $fileName, $tooltip);
        $this->title = $title;

        return $this;
    }

    /**
     * @param array $entities
     * @return Response
     */
    public function process($entities)
    {
        return $this
            ->prepare()
            ->displayTitle()
            ->makeTable($entities)
            ->export();
	}

    /**
     * @return PdfExport
     */
    private function prepare()
    {
        $this->tcpdf->SetCreator(PDF_CREATOR);
        $this->tcpdf->SetAuthor('');
        $this->tcpdf->SetTitle($this->fileName);
        $this->tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->tcpdf->setFontSubsetting(true);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('L','A4', FALSE, FALSE);
        $this->tcpdf->SetLineWidth(0.2);
        $this->tcpdf->SetDrawColor(0,0,0);

        return $this;
    }

    /**
     * @return PdfExport
     */
    private function displayTitle()
    {
        if ($this->title) {
            $html = '<h3 style="text-align: center;">'.$this->title.'</h3><br>';
            $this->tcpdf->SetFont('dejavusans', 'B', 12, '', true);
            $this->tcpdf->writeHTML($html,true,false,true,false,'');

        }
        return $this;
    }

    /**
     * @param array $entities
     * @return PdfExport
     */
    private function makeTable($entities)
    {
        $this->tcpdf->SetFont('dejavusans', 'N', 10, '', true);
        $header = $this->displayHeader();
        $this->displayRows($entities, $header);

        return $this;

    }

    /**
     * @return ExportHeader
     */
    private function displayHeader()
    {
        $header = $this->getHeader();
        $this->tcpdf->setFillColor(165, 165, 165);
        foreach ($header->getCells() as $h) {
            $this->cell($h->getValue(), $h->getWidth(), 1);
        }
        $this->tcpdf->Ln();

        return $header;
    }

    /**
     * @param array $entities
     * @param ExportHeader $header
     * @return PdfExport
     */
    private function displayRows($entities, $header)
    {
        foreach ($entities as $entity) {
            $row = $this->getRow($entity);
            foreach ($row->getCells() as $i => $cell) {
                $this->cell($cell->getValue(), $header->getHeaderWidth($i));
            }
            $this->tcpdf->Ln();
        }

        return $this;
    }

    /**
     * @return Response
     */
    private function export()
    {
        $response = new Response($this->tcpdf->Output($this->fileName.'.pdf',"D"));

        $response->headers->set('Content-Type', 'application/pdf');
        $response->sendHeaders();

        return $response;
    }

    /**
     * @param string|int $value
     * @param int $width
     * @param int $fill
     * @return PdfExport
     */
    private function cell($value, $width, $fill = 0)
    {
        $this->tcpdf->Cell($width, 7, $value, 1, 0, 'C', $fill, '', 1);

        return $this;
    }
}

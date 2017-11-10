<?php

namespace MottaPgBundle\Util\Export;

use Symfony\Component\HttpFoundation\Response;

/**
* ExcelExport
*/
class ExcelExport extends Export
{
    /**
     * @return ExcelExport
     */
	function __construct()
	{
		$this->type = 'excel';

		return $this;
	}

    /**
     * @param int $i
     * @param string $headerCall
     * @param string $rowCall
     * @param string $fileName
     * @param string $tooltip
     * @return ExcelExport
     */
    public function create($i, $headerCall, $rowCall, $fileName, $tooltip)
    {
    	parent::init($i, $headerCall, $rowCall, $fileName, $tooltip);

    	return $this;
    }

    /**
     * @param array $entities
     * @return Response
     */
    public function process($entities)
    {
        $xls = $this->prepare();
        $xls .= $this->makeTable($entities);
        return $this->export($xls);
	}

    /**
     * @return string
     */
    private function prepare()
    {
        return '<!DOCTYPE html>
                <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body>
                        <table>
                            <tbody>';
    }

    /**
     * @param array $entities
     * @return string
     */
    private function makeTable($entities)
    {
        return $this->displayHeader().$this->displayRows($entities);
    }

    /**
     * @return string
     */
    private function displayHeader()
    {
        $html = '<tr>';
        $header = $this->getHeader();
        foreach ($header->getCells() as $h) {
            $html .= '<th>'.$h->getValue().'</th>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * @param array $entities
     * @return string
     */
    private function displayRows($entities)
    {
        $html = '';
        foreach ($entities as $entity) {
            $html .= '<tr>';
            $row = $this->getRow($entity);
            foreach ($row->getCells() as $cell) {
                $html .= '<td>'.$cell->getValue().'</td>';
            }
            $html .= "</tr>";
        }

        return $html;
    }

    /**
     * @param string $xls
     * @return Response
     */
    private function export($xls)
    {
        $xls .= '</tbody>
                        </table>
                    </body>
                </html>';

        $response = new Response();
        $response->setContent($xls);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$this->fileName.'.xls"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}

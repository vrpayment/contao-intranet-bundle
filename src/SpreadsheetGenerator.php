<?php


namespace Vrpayment\ContaoIntranetBundle;


use Contao\System;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadsheetGenerator
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * @param string $lastModifiedBy
     * @param string $title
     * @param string $subject
     * @param string $description
     * @return $this
     */
    public function setProperties(string $lastModifiedBy, string $title, string $subject, string $description)
    {
        $this->spreadsheet->getProperties()
            ->setLastModifiedBy($lastModifiedBy)
            ->setTitle($title)
            ->setSubject($subject)
            ->setDescription($description);
        return $this;
    }

    /**
     * @param array $properties
     * @param int $activeSheetIndex
     * @return $this
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setSheetRow(array $properties, int $activeSheetIndex = 0)
    {
        foreach ($properties as $coordinate => $property) {
            $this->spreadsheet->setActiveSheetIndex($activeSheetIndex)->setCellValue($coordinate, $property);
        }

        return $this;
    }

    /**
     * @param string $filename
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getFileOutputXls(string $filename)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '_' . date('d-m-Y-H:i:s', time()) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function saveFileOutputXls(string $pathtoFile)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($pathtoFile);
    }
}

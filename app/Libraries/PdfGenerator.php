<?php

namespace App\Libraries;

if (! class_exists('TCPDF')) {
    $tcpdfPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'tecnickcom' . DIRECTORY_SEPARATOR . 'tcpdf' . DIRECTORY_SEPARATOR . 'tcpdf.php';
    if (is_file($tcpdfPath)) {
        require_once $tcpdfPath;
    }
}

use TCPDF;

class PdfGenerator extends TCPDF
{
    private $headerTitle = '';
    private $headerSubtitle = '';
    private $companyName = 'UDD PMI Kota Bekasi';
    private $companyAddress = 'Jl. Pramuka No. 01 Margajaya Bekasi Selatan';
    private $companyPhone = 'Telp 021-88960247';

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        
        // Set margins
        $this->SetMargins(15, 40, 15);
        $this->SetAutoPageBreak(true, 20);
        
        // Set font
        $this->SetFont('helvetica', '', 10);
    }

    public function setHeaderInfo($title, $subtitle = '')
    {
        $this->headerTitle = $title;
        $this->headerSubtitle = $subtitle;
    }

    public function Header()
    {
        // Background color for header
        $this->SetFillColor(0, 102, 204); // Blue
        $this->Rect(0, 0, $this->w, 35, 'F');

        // White text
        $this->SetTextColor(255, 255, 255);
        
        // Company name - main title
        $this->SetFont('helvetica', 'B', 13);
        $this->SetXY(15, 5);
        $this->Cell(0, 5, $this->companyName, 0, 1, 'L');
        
        // Address line 1
        $this->SetFont('helvetica', '', 9);
        $this->SetXY(15, 10);
        $this->Cell(0, 4, $this->companyAddress, 0, 1, 'L');
        
        // Phone
        $this->SetXY(15, 14);
        $this->Cell(0, 4, $this->companyPhone, 0, 1, 'L');
        
        // Divider line
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.5);
        $this->Line(15, 19, $this->w - 15, 19);
        
        // Report title
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(15, 21);
        $this->Cell(0, 5, $this->headerTitle, 0, 1, 'L');
        
        // Subtitle with date if available
        if ($this->headerSubtitle) {
            $this->SetFont('helvetica', '', 9);
            $this->SetXY(15, 27);
            $this->Cell(0, 4, 'Tanggal: ' . $this->headerSubtitle, 0, 1, 'L');
        }
        
        // Reset text color
        $this->SetTextColor(0, 0, 0);
        $this->SetLineWidth(0.3);
    }

    public function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(100, 100, 100);
        
        // Divider line
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->getY(), $this->w - 15, $this->getY());
        
        $this->SetY(-15);
        
        // Left: Generated date
        $this->SetX(15);
        $this->Cell(80, 5, 'Generated: ' . date('d M Y H:i'), 0, 0, 'L');
        
        // Center: Company name
        $this->SetX(50);
        $this->Cell(90, 5, 'UDD PMI Kota Bekasi', 0, 0, 'C');
        
        // Right: Page number
        $this->SetX($this->w - 35);
        $this->Cell(30, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    public function addTitle($text, $fontSize = 16)
    {
        $this->SetFont('helvetica', 'B', $fontSize);
        $this->SetTextColor(0, 102, 204);
        $this->Ln(5);
        $this->MultiCell(0, 8, $text, 0, 'L');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(3);
    }

    public function addSectionTitle($text, $fontSize = 12)
    {
        $this->SetFont('helvetica', 'B', $fontSize);
        $this->SetFillColor(230, 240, 250);
        $this->Cell(0, 8, $text, 1, 1, 'L', true);
        $this->SetFont('helvetica', '', 10);
        $this->Ln(2);
    }

    public function getAvailablePageWidth()
    {
        return $this->getPageWidth() - $this->lMargin - $this->rMargin;
    }

    public function addTable($headers, $data, $columnWidths = null)
    {
        $availableWidth = $this->getAvailablePageWidth();

        if (!$columnWidths) {
            $columnCount = count($headers);
            $columnWidths = array_fill(0, $columnCount, $availableWidth / $columnCount);
        }

        $totalWidth = array_sum($columnWidths);
        if ($totalWidth > $availableWidth) {
            $ratio = $availableWidth / $totalWidth;
            foreach ($columnWidths as $index => $width) {
                $columnWidths[$index] = $width * $ratio;
            }

            // Adjust rounding difference on the last column
            $difference = $availableWidth - array_sum($columnWidths);
            if ($difference !== 0) {
                $columnWidths[count($columnWidths) - 1] += $difference;
            }
        }

        $this->Ln(2);

        // Header
        $this->SetFont('helvetica', 'B', 9);
        $this->SetFillColor(0, 102, 204);
        $this->SetTextColor(255, 255, 255);

        foreach ($headers as $i => $header) {
            $this->MultiCell($columnWidths[$i], 8, $header, 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        }
        $this->Ln();

        // Data rows
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(0, 0, 0);
        $alternate = false;

        foreach ($data as $row) {
            if ($alternate) {
                $fillColor = [240, 248, 255];
            } else {
                $fillColor = [255, 255, 255];
            }
            $alternate = !$alternate;

            $rowHeight = 0;
            foreach ($row as $i => $cell) {
                $lineCount = $this->getNumLines($cell, $columnWidths[$i]);
                $rowHeight = max($rowHeight, $lineCount * 5 + 2);
            }

            if ($this->GetY() + $rowHeight > $this->PageBreakTrigger) {
                $this->AddPage();
                $this->SetFont('helvetica', 'B', 9);
                $this->SetFillColor(0, 102, 204);
                $this->SetTextColor(255, 255, 255);
                foreach ($headers as $i => $header) {
                    $this->MultiCell($columnWidths[$i], 8, $header, 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                }
                $this->Ln();
                $this->SetFont('helvetica', '', 8);
                $this->SetTextColor(0, 0, 0);
            }

            $this->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
            foreach ($row as $i => $cell) {
                $this->MultiCell($columnWidths[$i], $rowHeight, $cell, 1, 'L', true, 0, '', '', true, 0, false, true, $rowHeight, 'M');
            }
            $this->Ln();
        }

        $this->Ln(2);
    }

    public function addSummaryBox($label, $value, $x = null, $y = null)
    {
        $originalX = $this->GetX();
        $originalY = $this->GetY();

        if ($x !== null && $y !== null) {
            $this->SetXY($x, $y);
        }

        // Background
        $this->SetFillColor(230, 240, 250);
        $this->Rect($this->GetX(), $this->GetY(), 50, 25, 'F');

        // Label
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(50, 6, $label, 0, 1, 'C');

        // Value
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(0, 102, 204);
        $this->Cell(50, 14, $value, 0, 1, 'C');

        if ($x === null && $y === null) {
            $this->SetXY($originalX + 55, $originalY);
        }

        $this->SetTextColor(0, 0, 0);
    }

    public function addStats($stats)
    {
        $this->Ln(2);
        $this->SetFont('helvetica', 'B', 11);
        $this->SetFillColor(0, 102, 204);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 8, '  STATISTIK', 0, 1, 'L', true);
        
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);

        foreach ($stats as $stat) {
            $this->SetFillColor(245, 245, 245);
            $this->Cell(130, 7, '  ' . $stat['label'], 1, 0, 'L', true);
            $this->SetFillColor(220, 235, 255);
            $this->Cell(40, 7, $stat['value'], 1, 1, 'R', true);
        }

        $this->Ln(4);
    }
}

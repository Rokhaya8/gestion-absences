<?php
require_once __DIR__ . '/fpdf/fpdf.php';

function initPDF(string $titre = "Rapport d'absences", string $filiere = "", string $module = "") {
    $pdf = new FPDF();
    $pdf->AddPage();

    // Logo à gauche
    $pdf->Image(__DIR__ . '/../assets/logo_ensa.png', 10, 6, 30);

    // Déplacement à droite pour le titre
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(40); // Décalage horizontal
    $pdf->Cell(110, 10, utf8_decode($titre), 0, 1, 'C');

    if ($filiere !== "") {
        $pdf->Cell(40);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(110, 8, utf8_decode("Filière : $filiere"), 0, 1, 'C');
    }

    if ($module !== "") {
        $pdf->Cell(40);
        $pdf->Cell(110, 8, utf8_decode("Module : $module"), 0, 1, 'C');
    }

    $pdf->Ln(10); // Espace après le bloc titre
    return $pdf;
}



function addTableHeader(FPDF $pdf) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(220, 220, 220);

    $pdf->Cell(45, 10, mb_convert_encoding('Nom', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(45, 10, mb_convert_encoding('Prénom', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(30, 10, mb_convert_encoding('Apogée', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(35, 10, mb_convert_encoding('Module', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
    $pdf->Cell(35, 10, mb_convert_encoding('Filière', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);
}

function addTableRow(FPDF $pdf, array $row) {
    $pdf->SetFont('Arial', '', 11);

    $pdf->Cell(45, 10, mb_convert_encoding($row['nom'], 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(45, 10, mb_convert_encoding($row['prenom'], 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(30, 10, mb_convert_encoding($row['num_apogee'], 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(35, 10, mb_convert_encoding($row['module'], 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(35, 10, mb_convert_encoding($row['filiere'], 'ISO-8859-1', 'UTF-8'), 1, 1);
}

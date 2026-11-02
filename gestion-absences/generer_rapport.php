<?php
require_once '../includes/pdf_functions.php';
require_once '../config/db.php';

/* ── 1. Paramètres du formulaire ───────────────────────── */
$filiere = $_POST['filiere'] ?? '';
$module  = $_POST['module']  ?? '';

if (empty($filiere) || empty($module)) {
    exit("❌ Veuillez sélectionner une filière et un module.");
}

/* ── 2. Requête SQL ────────────────────────────────────── */
$sql = "
  SELECT  e.nom,
          e.prenom,
          e.num_apogee,
          f.nom  AS filiere,
          m.nom  AS module
  FROM      absences a
  JOIN      etudiants  e ON a.etudiant_id = e.id
  JOIN      seances    s ON a.seance_id  = s.id
  JOIN      modules    m ON s.module_id  = m.id
  JOIN      filieres   f ON e.filiere_id = f.id
  WHERE     f.nom = :filiere
        AND m.nom = :module
  ORDER BY  e.nom, e.prenom
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':filiere' => $filiere, ':module' => $module]);
$absences = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ── 3. Création du PDF ────────────────────────────────── */
$pdf = initPDF('Rapport d\'absences', $filiere, $module);
addTableHeader($pdf);

foreach ($absences as $row) {
    addTableRow($pdf, $row);
}

/* ── 4. Pied de page : date de génération ─────────────── */
$pdf->SetY(-20);                             // place le curseur 20 mm avant bas de page
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(
    0,
    10,
    mb_convert_encoding('Date de génération : ', 'ISO-8859-1', 'UTF-8') .
    date('d/m/Y'),
    0,
    0,
    'R'
);

/* ── 5. Sauvegarde + téléchargement ───────────────────── */
$filename = "absences_{$filiere}_{$module}.pdf";
$pdf->Output('F', __DIR__ . "/../pdf/$filename"); // enregistre dans /pdf/
$pdf->Output('D', $filename);                     // télécharge dans le navigateur

<?php
// Architect's Debugging HUD - Kills the silent 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/connection.php';

// LOCAL FPDF HANDSHAKE
require('includes/fpdf/fpdf.php'); 

if (!isset($_SESSION['user_id'])) { 
    die("UNAUTHORIZED_ACCESS_TERMINATED"); 
}

$uid = $_SESSION['user_id'];

// DATA EXTRACTION - Verified against Master Intel Map
$u_res = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$uid'");
$u_data = mysqli_fetch_assoc($u_res);

$r_res = mysqli_query($conn, "SELECT * FROM student_resume_data WHERE student_id = '$uid'");
$r_data = mysqli_fetch_assoc($r_res);

class SupremeResume extends FPDF {
    function Header() {
        global $u_data;
        // Geometric Header Branding
        $this->SetFillColor(15, 23, 42); 
        $this->Rect(0, 0, 210, 60, 'F'); // Increased height for better spacing
        
        $this->SetY(15);
        $this->SetFont('Arial', 'B', 28);
        $this->SetTextColor(255, 255, 255);
        
        // 1. PRIMARY IDENTITY: Display Full Name instead of Roll No at the top
        $name = !empty($u_data['full_name']) ? $u_data['full_name'] : 'SUBJECT_UNNAMED';
        $this->Cell(0, 15, strtoupper($name), 0, 1, 'C');
        
        $this->SetY($this->GetY() + 2);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(148, 163, 184);
        
        // 2. METADATA ROW: Displaying the verified Roll No and Branch
        // Ensure $u_data['roll_no'] is correctly populated in your DB
        $roll = !empty($u_data['roll_no']) ? strtoupper($u_data['roll_no']) : 'ID_PENDING';
        $branch = !empty($u_data['branch']) ? $u_data['branch'] : 'UNIT_GEN';
        
        $meta = $u_data['email'] . "  |  " . $branch . " UNIT  |  ROLL: " . $roll;
        $this->Cell(0, 5, $meta, 0, 1, 'C');
        
        $this->Ln(20);
    }
    function SectionHeader($title) {
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(30, 64, 175); 
        $this->Cell(0, 10, strtoupper($title), 0, 1, 'L');
        $this->SetDrawColor(59, 130, 246);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(4);
    }
}

$pdf = new SupremeResume();
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// 1. PROFESSIONAL BRIEF
$pdf->SectionHeader('Professional Brief');
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 65, 85);
$summary = !empty($r_data['professional_summary']) ? $r_data['professional_summary'] : 'Technical architect focused on engineering scalable solutions.';
$pdf->MultiCell(0, 6, $summary, 0, 'L');
$pdf->Ln(6);

// 2. TECHNICAL SKILLS MATRIX
$pdf->SectionHeader('Technical Expertise Matrix');
$pdf->SetFont('Arial', '', 10);
$skills_raw = !empty($r_data['skills_json']) ? $r_data['skills_json'] : 'Systems, Logic, Deployment';
$skills = explode(',', $skills_raw);

foreach($skills as $skill) {
    $pdf->SetTextColor(59, 130, 246);
    $pdf->Cell(5, 7, chr(149), 0, 0); 
    $pdf->SetTextColor(51, 65, 85);
    $pdf->Cell(0, 7, trim($skill), 0, 1);
}
$pdf->Ln(5);

// 3. ACADEMIC CREDENTIALS
$pdf->SectionHeader('Academic Background');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, 'Rustamji Institute of Technology (RJIT)', 0, 1);
$pdf->SetFont('Arial', '', 10);

// FIX: Handing the "202023" error. 
// If DB year is 2 digits (23), this makes "2023".
$db_year = $u_data['batch_year'];
$clean_year = (strlen($db_year) <= 2) ? "20" . $db_year : $db_year;

$pdf->Cell(0, 6, 'B.Tech in ' . $u_data['branch'] . ' - Class of ' . $clean_year, 0, 1);
$pdf->Ln(10);

// 4. VAULT ACHIEVEMENTS
$pdf->SectionHeader('Verified Vault Achievements');
$v_query = mysqli_query($conn, "SELECT doc_title FROM vault_items WHERE student_id = '$uid'");
if(mysqli_num_rows($v_query) > 0) {
    while($v = mysqli_fetch_assoc($v_query)) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(59, 130, 246);
        $pdf->Cell(8, 7, '>', 0, 0); 
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(51, 65, 85);
        $pdf->Cell(0, 7, $v['doc_title'], 0, 1);
    }
} else {
    $pdf->Cell(0, 7, 'No verified vault achievements indexed.', 0, 1);
}

$pdf->Output('I', 'Resume_' . $u_data['roll_no'] . '.pdf');
?>

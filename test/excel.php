<?php
require '../packages/vendor/autoload.php'; // Ensure Composer autoloader is included

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the title of the sheet
$sheet->setTitle('Attendance');

// Add the college logo
$drawing = new Drawing();
$drawing->setName('College Logo');
$drawing->setDescription('College Logo');
$drawing->setPath('../global/images/college_logo.png'); // Replace with your logo path
$drawing->setHeight(100); // Adjust the height of the logo
$drawing->setCoordinates('H1'); // Set the position of the logo
$drawing->setOffsetX(10);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

// Set the college name and details in merged cells
$sheet->mergeCells('F5:N5');
$sheet->setCellValue('f5', 'Sri Venkateshwaraa College of Engineering & Technology');
$sheet->getStyle('F5')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set other college details
$sheet->mergeCells('F6:N6');
$sheet->setCellValue('F6', 'Department of Computer Science and Engineering');
$sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('F7:N7');
$sheet->setCellValue('F7', 'Academic Year : 2020-2021');
$sheet->getStyle('F7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('E9:I9');
$sheet->setCellValue('E9', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('E9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

$sheet->mergeCells('E10:I10');
$sheet->setCellValue('E10', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('E10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

$sheet->mergeCells('E11:I11');
$sheet->setCellValue('E11', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('E11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

$sheet->mergeCells('E12:I12');
$sheet->setCellValue('E12', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('E12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

$sheet->mergeCells('K9:O9');
$sheet->setCellValue('K9', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('K9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->mergeCells('K10:O10');
$sheet->setCellValue('K10', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('K10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->mergeCells('K11:O11');
$sheet->setCellValue('K11', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('K11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->mergeCells('K12:O12');
$sheet->setCellValue('K12', 'Batch: 2021-2015, Year / Sem: IV / VII');
$sheet->getStyle('K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Add headers
$sheet->setCellValue('B14', 'Sl.No');
$sheet->setCellValue('C14', 'Register No.');
$sheet->setCellValue('D14', 'Student Name');
$sheet->setCellValue('E14', 'Mentor Staff');
$sheet->setCellValue('F14', 'Total No. Days');
$sheet->mergeCells('G14:N14');
$sheet->setCellValue('G14', '21-06-2024');
$sheet->mergeCells('O14:V14');
$sheet->setCellValue('O14', '22-06-2024');

// Style the headers
$sheet->getStyle('B14:H14')->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF2196F3'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
]);

// Sample data for students' attendance
$data = [
    ['1', '21TD0601', 'Ramesh V', 'Mr. Ragul', '90', 'P', 'P'],
    ['2', '21TD0602', 'Ravi K', 'Mr. Ragul', '90', 'A', 'P'],
    ['3', '21TD0603', 'Kannan', 'Mr. Ragul', '90', 'P', 'P'],
    ['4', '21TD0604', 'Puviarasan', 'Mr. Ragul', '90', 'P', 'P'],
    ['5', '21TD0605', 'Anandhavel', 'Mr. Ragul', '90', 'P', 'P'],
    // Add more rows as needed
];

// Populate data into the spreadsheet
$row = 16; // Starting row for the data
foreach ($data as $student) {
    for ($col = 0; $col < count($student); $col++) {
        // Use the correct method to set cell values by column and row
        $sheet->setCellValue(chr($col + 1 + 65) . $row, $student[$col]); // chr converts number to character (A, B, C...)
    }

    // Apply borders to all cells
    $sheet->getStyle("B{$row}:H{$row}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,


        ],
    ]);

    // Apply background color based on odd/even row
    $bgColor = ($row % 2 == 0) ? 'FFF1F8E9' : 'FFB2EBF2';
    $sheet->getStyle("B{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);

    $row++;
}

// Set headers to trigger download in the browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="attendance_report.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;

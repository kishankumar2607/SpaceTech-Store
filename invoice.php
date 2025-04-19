<?php
require_once 'includes/db.php';
require_once 'includes/fpdf/fpdf.php';

$order_id = $_GET['order_id'] ?? 0;

// Fetch order and customer info
$stmt = $pdo->prepare("SELECT o.*, u.name, u.address FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Invalid order ID.");
}

// Fetch order items
$stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$pdf = new FPDF();
$pdf->AddPage();

// === Header Branding ===
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(0, 0, 128); // Navy blue
$pdf->Cell(0, 12, 'SpaceTech Electronics', 0, 1, 'C');

$pdf->SetFont('Arial', '', 14);
$pdf->SetTextColor(0);
$pdf->Cell(0, 10, 'Invoice Summary', 0, 1, 'C');
$pdf->Ln(5);

// === Order Info ===
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, "Customer: " . $order['name'], 0, 1);
$pdf->Cell(100, 10, "Address: " . $order['address'], 0, 1);
$pdf->Cell(100, 10, "Order ID: #" . $order_id, 0, 1);
$pdf->Ln(5);

// === Table Header ===
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(0, 0, 128);
$pdf->SetTextColor(255);
$pdf->Cell(90, 10, 'Product', 1, 0, 'L', true);
$pdf->Cell(30, 10, 'Qty', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Unit Price', 1, 0, 'R', true);
$pdf->Cell(30, 10, 'Subtotal', 1, 1, 'R', true);

// === Table Rows ===
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0);
$fill = false;
$total = 0;

foreach ($items as $item) {
    $subtotal = $item['quantity'] * $item['price'];
    $total += $subtotal;

    $pdf->SetFillColor(245, 245, 245); // light gray
    $pdf->Cell(90, 10, $item['name'], 1, 0, 'L', $fill);
    $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 10, '$' . number_format($item['price'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(30, 10, '$' . number_format($subtotal, 2), 1, 1, 'R', $fill);
    $fill = !$fill;
}

// === Total Row ===
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(220, 220, 220); // Total row gray
$pdf->Cell(160, 10, 'Total', 1, 0, 'R', true);
$pdf->Cell(30, 10, '$' . number_format($total, 2), 1, 1, 'R', true);

// === Footer Message ===
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100);
$pdf->MultiCell(0, 8, "Thank you for shopping with SpaceTech!\nThis invoice was auto-generated on " . date("Y-m-d") . ".", 0, 'C');

// Output PDF inline in browser
$pdf->Output('I', 'invoice_' . $order_id . '.pdf');
exit;
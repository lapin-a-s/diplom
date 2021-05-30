<?php
require_once 'tcpdf/tcpdf.php';
require_once 'phpQuery/phpQuery/phpQuery.php';
//if (!empty($_POST['linkforanalis']))
$links = $_GET['link'];
$doc = phpQuery::newDocument(file_get_contents($links));
$countError = 0;
$entry = $doc->find('title');
$data['title'] = pq($entry)->text();
$stringtitle = strlen($data['title']);
if ($stringtitle < 30 || $stringtitle > 65) {
    $countError++;
}
$title = $data['title'];
$counttitle = strlen($title);
$entry = $doc->find('head meta[name="keywords"]');
$data['keywords'] = pq($entry)->attr('content');
if (pq($doc['keywords'])->length() > 6) {
    $countError++;
}
$keywords = $data['keywords'];
$countkeywords = strlen($keywords);
$entry = $doc->find('head meta[name="description"]');
$data['description'] = pq($entry)->attr('content');
$stringdescription = strlen($data['description']);
if ($stringdescription < 70 || $stringdescription > 170) {
    $countError++;
}
$description = $data['description'];
$countdescription = strlen($description);
$entry = $doc->find('h1');
if (pq($doc['h1'])->length() == 0) {
    $countError++;
}
$data['h1'] = pq($entry)->text();
$string = strlen($data['h1']);
if ($stringtitle < 30 || $stringtitle > 65) {
    $countError++;
}
$h1 = $data['h1'];

$entry = $doc->find('a');
foreach ($entry as $row) {
    $data['a'][] = pq($row)->attr('rel');
}
$Countnore = 0;
foreach ($data['a'] as $row) {
    if ($row == "nofollow") {
        $row . "<br>\r\n";
        $Countnore++;
    }
}
if ($Countnore == 0) {
    $countError++;
}

$url = $links;
if (strpos($url, 'http') !== FALSE) {
    $url_array = parse_url($url); // разбивка URL на части
    $url = $url_array['host'];
}
$ip = gethostbyname($url); // IP по доменному имени
if ($ip == $url) { //
    $ip = FALSE;
}

$xml = simplexml_load_string(file_get_contents('http://rest.db.ripe.net/search?query-string=' . $ip));
$array = json_decode(json_encode($xml), TRUE);

$data = array();
foreach ($array['objects'] as $row) {
    foreach ($row as $row2) {
        foreach ($row2['attributes'] as $row3) {
            foreach ($row3 as $row4) {
                $data[$row4['@attributes']['name']][] = $row4['@attributes']['value'];
            }
        }
    }
}
$host = $ip;
$json = file_get_contents('http://ip-api.com/json/' . $host . '?lang=ru');
$arraywhois = json_decode($json, TRUE);

libxml_use_internal_errors(true);
$doc = simplexml_load_string($links);
$xml = explode("\n", $links);
$flag = 0;
if (!$doc) {
    $errors = array_reverse(libxml_get_errors());
    foreach ($errors as $error) {
        $flag++;
    }
    libxml_clear_errors();
}


function display_xml_error($error, $xml)
{
    $return = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Предупреждения $error->code: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "Ошибки $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Критические ошибки $error->code: ";
            break;
    }

    $return .= trim($error->message);

    if ($error->file) {
        $return .= "\n  Файл: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
}

if ($flag == 0)
    $validedocument = "Документ валидный, ошибок нет.";
else
    $validedocument = "Документ не валидный, ошибок:" . $flag;

$doc = phpQuery::newDocument(file_get_contents($links));
$data['css'] = array();

$entry = $doc->find('head link[rel="stylesheet"]');
foreach ($entry as $row) {
    $data['css'][] = pq($row)->attr('href');
}
$countcss = 0;
foreach ($data['css'] as $row) {
    $countcss++;
}

$data['linksall'] = array();
$entry = $doc->find('head link');
foreach ($entry as $row) {
    $data['linksall'][] = pq($row)->attr('href');
}
$count = 0;
foreach ($data['linksall'] as $row) {
    $count++;
}
$countall = $count + $countcss;

$textColour = array(0, 0, 0);
$headerColour = array(100, 100, 100);
$tableHeaderTopTextColour = array(255, 255, 255);
$tableHeaderTopFillColour = array(125, 152, 179);
$tableHeaderTopProductTextColour = array(0, 0, 0);
$tableHeaderTopProductFillColour = array(143, 173, 204);
$tableHeaderLeftTextColour = array(99, 42, 57);
$tableHeaderLeftFillColour = array(184, 207, 229);
$tableBorderColour = array(50, 50, 50);
$tableRowFillColour = array(213, 170, 170);
$reportName = "Анализируемый сайт: " . $title;
$reportNameYPos = 10;
$logoFile = "photo/brand.png";
$logoXPos = 50;
$logoYPos = 38;
$logoWidth = 110;
$columnLabels = array("Количество");
$dataRows = array($countError, $validedocument, $countall, $Countnore);
$rowLabels = array("Количество технических ошибок", "Количество ошибок валидации", "Количество ссылок", "Количество noreffer");
$counlinkss = array("Уникальных", "Css");
$chartXPos = 20;
$chartYPos = 250;
$chartWidth = 160;
$chartHeight = 80;
$chartXLabel = "Ссылки";
$chartYLabel = "Статистика ссылок";
$chartYStep = 20000;
$chartColours = array(
    array(255, 100, 100),
    array(100, 255, 100),
    array(100, 100, 255),
    array(255, 255, 100),
);
$data = array(
    array(9940, 10100, 9490, 11730),
    array(19310, 21140, 20560, 22590),
    array(25110, 26260, 25210, 28370),
    array(27650, 24550, 30040, 31980),
);

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
$pdf->SetFont('freesans', '', 12);
$pdf->AddPage();
$pdf->Image($logoFile, $logoXPos, $logoYPos, $logoWidth);
$pdf->Ln($reportNameYPos);
$pdf->Cell(0, 15, $reportName, 0, 0, 'C');
$pdf->Ln(90);
$pdf->SetFont('freesans', '', 12);
$pdf->Write(6, "Описание:" . $description);

$pdf->SetDrawColor($tableBorderColour[0], $tableBorderColour[1], $tableBorderColour[2]);
$pdf->Ln(15);
$pdf->SetFont('freesans', 'B', 15);
$pdf->SetTextColor($tableHeaderTopProductTextColour[0], $tableHeaderTopProductTextColour[1], $tableHeaderTopProductTextColour[2]);
$pdf->SetFillColor($tableHeaderTopProductFillColour[0], $tableHeaderTopProductFillColour[1], $tableHeaderTopProductFillColour[2]);
$pdf->Cell(80, 12, "Свод о странице", 1, 0, 'L', true);
// Остальные ячейки заголовков
$pdf->SetTextColor($tableHeaderTopTextColour[0], $tableHeaderTopTextColour[1], $tableHeaderTopTextColour[2]);
$pdf->SetFillColor($tableHeaderTopFillColour[0], $tableHeaderTopFillColour[1], $tableHeaderTopFillColour[2]);
for ($i = 0; $i < count($columnLabels); $i++) {
    $pdf->Cell(85, 12, $columnLabels[$i], 1, 0, 'C', true);
}
$pdf->Ln(12);
$fill = false;
$row = 0;
foreach ($data as $dataRow) {
    // Создаем левую ячейку с заголовком строки
    $pdf->SetFont('freesans', 'B', 15);
    $pdf->SetTextColor($tableHeaderLeftTextColour[0], $tableHeaderLeftTextColour[1], $tableHeaderLeftTextColour[2]);
    $pdf->SetFillColor($tableHeaderLeftFillColour[0], $tableHeaderLeftFillColour[1], $tableHeaderLeftFillColour[2]);
    $pdf->Cell(80, 12, " " . $rowLabels[$row], 1, 0, 'L', $fill);
    // Создаем ячейки с данными
    $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
    $pdf->SetFillColor($tableRowFillColour[0], $tableRowFillColour[1], $tableRowFillColour[2]);
    $pdf->SetFont('freesans', '', 15);
    $pdf->Cell(85, 12, " " . $dataRows[$row], 1, 0, 'L', $fill);
    $row++;
    $fill = !$fill;
    $pdf->Ln(12);
}
$pdf->Ln(90);
$pdf->SetFont('freesans', '', 12);
foreach ($arraywhois as $key => $value) {
    $whois = $key . ':' . $value . "
    ";
    $pdf->Write(6, $whois);
}
    ob_end_clean();
    $pdf->Output('test.pdf');
?>
<!doctype html>
<html lang="en">
<head>
    <title>Seo-Аудит</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link rel="stylesheet" href="mainwindow.css">
</head>
<body background="photo/photo_fone.jpg" >
 <form method="post" id="formforanalyse">

<div class="jumbotron">
    <div class="container">
        <h1 class="display-6">Проанализируйте сайт</h1>
    </div>
</div>
    <div class="input-group mb-2">
        <input type="text" class="form-control" placeholder="Url-Ссылка" value="<?php echo $_POST['Url-link'] ?>" aria-label="Url-Ссылка" id="Url-link" name="Url-link" aria-describedby="basic-addon2">
        <div class="input-group-append">
            <button class="btn btn-outline-dark" name="buttonforan" type="submit">Анализировать</button>
        </div>
    </div>

 </form>
 <?php

require_once 'phpQuery/phpQuery/phpQuery.php';
//if (!empty($_POST['linkforanalis']))
    $links = $_POST['Url-link'];
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
    $entry = $doc->find('h1');
    $Counth1 = 0;
    foreach ($entry as $row) {
        $data['H1'][] = pq($row)->find('h1');
        $Counth1++;
    }
    if ($Counth1 > 1) {
        $countError++;
    }
    $entry = $doc->find('h2');
    $Counth2 = 0;
    foreach ($entry as $row) {
        $data['H2'][] = pq($row)->find('h2');
        $Counth2++;
    }
    if ($Counth2 == 0) {
        $countError++;
    }
    $entry = $doc->find('h3');
    $Counth3 = 0;
    foreach ($entry as $row) {
        $data['H3'][] = pq($row)->find('h3');
        $Counth3++;
    }
    $entry = $doc->find('h4');
    $Counth4 = 0;
    foreach ($entry as $row) {
        $data['H4'][] = pq($row)->find('h4');
        $Counth4++;
    }
    $entry = $doc->find('h5');
    $Counth5 = 0;
    foreach ($entry as $row) {
        $data['H5'][] = pq($row)->find('h5');
        $Counth5++;
    }
    $entry = $doc->find('h6');
    $Counth6 = 0;
    foreach ($entry as $row) {
        $data['H6'][] = pq($row)->find('h6');
        $Counth6++;
    }
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

?>
 <div id="Analysisinfo" >
 <a href="pdf.php?link=<?=$links?>">Пдф</a>

     <div class="shadow p-3 mb-5 bg-light rounded" id="infopanel" >
         <blockquote class="blockquote blockquote-reverse">
             <p class="lead">Информация о сайте</p>
             <p class="mb-0">Title <cite title="Source Title"><?php echo $counttitle;?></cite></p>
             <footer class="blockquote-footer"><?php echo $title;?></footer>
             <p class="mb-0">Description <cite title="Source Title"><?php echo $countdescription;?></cite></p>
             <footer class="blockquote-footer"><?php echo $description;?></footer>
             <p class="mb-0">Keywords <cite title="Source Title"><?php echo $countkeywords;?></cite></p>
             <footer class="blockquote-footer"><?php echo $keywords;?></footer>
             <p class="mb-0">Заголовки</p>
             <footer class="blockquote-footer"><?php echo "H1 (".$Counth1.") H2(".$Counth2.") H3(".$Counth3.") H4(".$Counth4.") H5(".$Counth5.") H6(".$Counth6.")"; echo $Countnore;?></footer>
             <p class="mb-0">Аттрибуты</p>
             <footer class="blockquote-footer">noreferrer (<?php echo $Countnore;?>), nofollow (<?php echo $Countnore;?>), noopener(<?php echo $Countnore;?>)</footer>
         </blockquote>
         <?php
         if($countError >0)
             {
                 echo "<div class='alert alert-danger' role='alert'>
             <strong>$countError</strong> технических ошибок обнаружено.
         </div>";
             }
         else
             {
                 echo "<div class='alert alert-success' role='alert'>
             технических ошибок не обнаружено.
         </div>";
             }
         if ($validedocument == "Документ валидный, ошибок нет."){
             echo "<div class='alert alert-success' role='alert'>
             Валидность анализируемого сайта:<strong>$validedocument</strong>
         </div>";
         }
         else{
             echo "<div class='alert alert-danger' role='alert'>
             Валидность анализируемого сайта:<strong>$validedocument</strong>
         </div>";
         }

     ?>
     </div>
 </div>
     <div class="shadow p-4 mb-4 bg-light rounded" id="Linkss" >
         <p class="mb-0">Ссылки css <cite title="Source Title"><?php echo $countcss;?></cite></p>
         <?php
         $entry = $doc->find('head link[rel="stylesheet"]');
         foreach ($entry as $row) {
             $data['css'][] = pq($row)->attr('href');
         }
         $countcss =0;
         foreach ($data['css'] as $row) {
             $countcss++;
         }
         ?>
         <footer class="blockquote-footer"><?php foreach ($data['css'] as $row) {
                 echo $row . "<br>\r\n";}?></footer>
         <p class="mb-0">Ссылки <cite title="Source Title"><?php echo $count;?></cite></p>
         <?php
         $data['linksall'] = array();
         $entry = $doc->find('head link');
         foreach ($entry as $row) {
             $data['linksall'][] = pq($row)->attr('href');
         }
         $count =0;
         foreach ($data['linksall'] as $row) {
             $count++;
         }
         ?>
         <footer class="blockquote-footer"><?php foreach ($data['linksall'] as $row) {
                 echo $row . "<br>\r\n";}?></footer>
         <canvas id="statistic" width="500" height="500"></canvas>
         <?php
         $months = array();
         $data = array();
         $data = [$count];
         $arrDatasets = array(
             'label' => "Статистика всех данных",
             'borderColor' => "#000",
             'backgroundColor' => "transparent",
             'pointBorderColor' => "#7FFF00",
             'pointBackgroundColor' => "#7FFF00",
             'pointRadius' => "5",
             'pointHoverRadius' => "10",
             'data'=>$data
         );
         $countall = $count + $countcss;
         ?>
     </div>
             <div class="shadow p-4 mb-4 bg-light rounded" id="Whois" >
                 <?php
                 $countwois = 0;
                 foreach ($arraywhois as $key => $value)
                    $countwois++; ?>
                 <p class="mb-0">Whois <cite title="Source Title"><?php echo $countwois;?></cite></p>
                 <footer class="blockquote-footer"><?php  foreach ($arraywhois as $key => $value)
                         echo $key . ':' . $value . '<br>'; ?></footer>
     </div>
 </div>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
 <script>
     var ctx = document.getElementById('statistic').getContext('2d');
     var countall = '<?php echo $count;?>';
     var countcss = '<?php echo $countcss;?>'
     var countins= '<?php echo $countall;?>'
     var count = '<?php echo "Статистика ссылок ".$countall;?>';
     var myLineChart = new Chart(ctx, {
         type: 'bar',
         data: {
             labels: ["Уникальных ссылок", "Css"],
             datasets: [{
                 label:count,
                 borderColor: '#000',
                 backgroundColor: [ "#808080", "#008000", "#FF0000", "#000000" ],
                 pointBorderColor: '#FFD700',
                 pointBackgroundColor: '#FFD700',
                 data: [countall,countcss,countins],
                 pointRadius: 5,
                 pointHoverRadius: 10,
             }]
         }
     });
     myLineChart.update;
 </script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<script src="mainwindow.js"></script>
</body>
</html>
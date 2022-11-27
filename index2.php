<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/style.css" rel="stylesheet">
<title>SP500 リアルタイムチャート</title>
</head>
<body>

<?php
// エラーを画面に表示(1を0にすると画面上にはエラーは出ない)
ini_set('display_errors',1);

// 全てのエラーを出力する
error_reporting(E_ALL);

$url = "https://nikkei225jp.com/chart/"; // 日経平均 リアルタイム チャート
$url2 = "https://nikkeiyosoku.com/spx/data/"; // S&P500：時系列/推移 ｜ 投資の森

// cURLセッションを初期化
$ch = curl_init();
$ch2 = curl_init();

// curlオプションを設定する
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// curl_exec()の戻り値を文字列にする

curl_setopt($ch2, CURLOPT_URL, $url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);// curl_exec()の戻り値を文字列にする

// URLの情報を取得して$htmlに保存
$html = curl_exec($ch);
$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); // 文字エンコード変換

if (curl_errno($ch)) {
    $error = curl_error($ch);
    echo "エラーです！", $error;
}

$html2 = curl_exec($ch2);
$html2 = mb_convert_encoding($html2, 'HTML-ENTITIES', "UTF-8"); // 文字エンコード変換

// セッションを終了
curl_close($ch);

curl_close($ch2);


// ■必要なデータをDOMやXPathで抽出
$dom = new DOMDocument;

@$dom->loadHTML($html);

// $h1 = $dom->getElementsByTagName("h1")->item(0);
$day = $dom->getElementById("wtime");


echo "<h6>";
echo $day->nodeValue;
echo "</h6>";

$xpath = new DOMXPath($dom);
$if_box2 = $xpath->query('//div[@class="if_box2"]');

// ダウ平均株価
$dow = $if_box2->item(1)->childNodes;
echo "<p><b>ダウ平均株価</b><br>￥";
echo $dow[0]->nodeValue;
echo "<br>";
$test = $dow[1]->nodeValue;
$test = (float)rtrim($dow[1]->nodeValue, "%");
if($test < 0) {
    echo "<span style='color:#f00;'>";
} else {
    echo "<span style='color:#00f;'>";
}
echo $dow[1]->nodeValue;
echo "</span></p>";

$dom2 = new DOMDocument;

@$dom2->loadHTML($html2);

$xpath2 = new DOMXPath($dom2);
print_r($xpath2);

$before = $xpath2->query('//table[@class="table-bordered table-striped"]/tbody/tr');
// $before = $xpath2->query('//table[@id="curr_table"]/tbody/tr');
print_r($before);

foreach ($before as $tr) {
	// echo $tr->nodeValue.'<br>'; // 日にちごとの各価格 // オブジェクトに配列の指定をしたら怒られる
	$box[] = $tr->nodeValue; // 日にちごと配列に // オブジェクトに配列の指定をしたら怒られる
}


$yesterday = $box[0];

echo "<p><b>時系列株価（終値）</b></p>";
echo "<table>";

$sum = 0;
$average = 0;
$numeral = 0;

function arrange($when){
    echo "<tr>";
    echo "<td>", substr($when, 0, 10), " </td>"; //日付部
    echo "<td>￥", substr($when, 43, 6), "</td>"; //株価部
    echo "</tr>";
    $substr = substr($when, 10, 6);
    
    // echo $substr, "\n";

    $numeral = (int)$substr;

    // echo $numeral;
    return $numeral;
}

for ($i = 0; $i <= 10; $i++) {
    arrange($box[$i]);
    // $sum = $numeral++;
}

// echo $sum;

// $average = $sum / 10;
// echo "平均株価 \\", $average;

echo "</table>";


// echo "<pre>";
// var_dump($array);
// echo "</pre>";

// 参考URL
// https://ai-inter1.com/xpath/
?>


</body>
</html>
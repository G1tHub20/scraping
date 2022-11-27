<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/style.css" rel="stylesheet">
<title>SP500 リアルタイム</title>
</head>
<body>
<a href="http://morismo.php.xdomain.jp/scraping/">>>日経平均</a>

<?php
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

$html2 = curl_exec($ch2);
$html2 = mb_convert_encoding($html2, 'HTML-ENTITIES', "UTF-8"); // 文字エンコード変換

// セッションを終了
curl_close($ch);
curl_close($ch2);

// ■必要なデータをDOMやXPathで抽出
$dom = new DOMDocument;
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$if_box2 = $xpath->query('//div[@class="if_box2"]');

$dom2 = new DOMDocument;
@$dom2->loadHTML($html2);
$xpath2 = new DOMXPath($dom2);

// 日時表示
$wtime = $dom->getElementById("wtime");
$time = $wtime->nodeValue;
$result = explode('■',$time);
echo "<h6>";
echo($result[0] . "■" . $result[1] . "■" . $result[3]); 
echo "</h6>";

// ダウ平均株価
$dow = $if_box2->item(1)->childNodes;
echo "<p><b>ダウ平均株価</b><br>￥";
echo $dow[0]->nodeValue;
echo "<br>";
$test = (float)rtrim($dow[1]->nodeValue, "%");
if($test < 0) {
  echo "<span style='color:#f00;'>";
} else {
  echo "<span style='color:#00f;'>";
}
echo $dow[1]->nodeValue;
echo "</span></p>";
echo "</p>";
?>


<!-- 時系列データ ==================== -->
<p><b>時系列データ（終値）</b></p>
<table>

<?php
$raw_tr = $xpath2->query('//table[@class="table table-bordered table-striped"]/tbody/tr');
foreach ($raw_tr as $tr) {
	$box[] = $tr->nodeValue; //日にちごと配列に //ある特定の要素の値（テキスト）を取得
}

function arrange($when){

  echo "<tr>";
//  echo "<td>", substr($when, 3, 9), "&nbsp;</td>"; //日付部
  echo "<td>", substr($when, 2, 9), "&nbsp;</td>"; //日付部
  echo "<td>$", substr($when, 38, 9), "</td>"; //終値部（現地時間16:00）
  echo "</tr>";
  $substr = substr($when, 10, 6);

  $numeral = (int)$substr;
  return $numeral;
}

for ($i = 0; $i <= 13; $i++) { //2週間分
  arrange($box[$i]);
}

?>

</table>

<br>
<footer>
引用元:<br>
<a href="https://nikkei225jp.com/chart/" target="_blank" rel="noopener noreferrer">日経平均 リアルタイム チャート</a>、
<a href="https://nikkeiyosoku.com/spx/data/" target="_blank" rel="noopener noreferrer">S&P500：時系列/推移 ｜ 投資の森</a>
</footer>

</body>
</html>
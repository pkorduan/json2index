<?php
  $config_file = 'conf/' . ($_REQUEST['c'] != '' ? $_REQUEST['c'] . '.ini' : 'config.ini');

  if (!file_exists($config_file)) {
    echo 'Konfigurationsdatei: ' . $config_file . ($_REQUEST['c'] != '' ? ' aus dem Parameter c' : '') . ' nicht gefunden.';
    exit;
  }

  $config = parse_ini_file($config_file, true);
  if (!file_exists($config['json']['source'])) {
    echo 'Die JSON-Datei: ' . $config['json']['source'] . ' konnte nicht gefunden werden. Bitte prüfen sie die Angabe source im Abschnitt json der Konfigurationsdatei: ' . $config_file . ' oder sorgen Sie dafür, dass die JSON-Datei an der angegeben Stelle verfügbar ist.';
  }
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>JSON to Index Converter</title>
</head>
<body>
<h1>Hello, welcome to the JSON to Index Converter.</h1>
  <h2>Content of ini file.</h2>
  <pre>
  <?php
    print_r($config);
  ?>
  </pre>
<?php
  $remove_newline = function($line) {
    return str_replace(array("\r", "\n"), '', $line);
  };

  # load config file with attributes
  $attributes = $config['json']['attributes'];

  # load config file with stopwords
  $stopWords = array_map(
    $remove_newline,
    file('conf/stopwords.txt', true)
  );

  ?><h2>Convert and Save JSON</h2>
  Read JSON File from <?php echo getcwd() . '/' . $config['json']['source']; ?>,<br>
  and save the resulting Index File in <?php echo getcwd() . '/' . $config['index']['localPath'] . $config['index']['fileName']; ?><br><?php
  
  # load data file
  $file_content = file_get_contents($config['json']['source']);
  $data = json_decode($file_content, true);

  $index = array(); # assiziatives Array mit index wörtern

  foreach($data AS $set) {
    foreach($set AS $key => $value) {
      if (in_array($key, $attributes)) {
        $words = explode(' ', $value);
        foreach($words AS $word) {
          $word = trim($word, " .,:\"/()-");
          if (!$config['index']['caseSensitive']) {
            $word = strtolower($word);
          }
          if ($word != '' && strlen($word) > 2 && !in_array(strtolower($word), $stopWords)) {
            if (array_key_exists($word, $index)) {
              if (!in_array($set[$config['json']['identifier']], $index[$word])) {
                array_push($index[$word], $set[$config['json']['identifier']]);
              }
            }
            else {
              $index[$word] = array($set[$config['json']['identifier']]);
            }
          }
        }
      }
    }
  }

  ksort($index);

  $json_text = json_encode($index);
  file_put_contents(
    getcwd() .
    '/' .
    $config['index']['localPath'] .
    $config['index']['fileName'],
    $json_text
  );
?>
  <br>This file is now available for download <a href="<?php echo $config['index']['webPath'] . $config['index']['fileName']; ?>" target="_blank">here</a>.<br>
<?php
foreach($index AS $word => $values) {
  echo '<br><b>' . $word . ':</b> ' . implode(', ', $values);
}
?>  
</body>
</html>
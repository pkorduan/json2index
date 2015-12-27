<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>JSON to Index Converter</title>
</head>
<body>
<?php
  $config = parse_ini_file('config.ini', true);

  $remove_newline = function($line) {
    return str_replace(array("\r", "\n"), '', $line);
  };

  # load config file with attributes
  $attributes = array_map(
    $remove_newline,
    file('attributes.txt', true)
  );

  # load config file with stopwords
  $stopWords = array_map(
    $remove_newline,
    file('stopwords.txt', true)
  );

  # load data file
  $file_content = file_get_contents("../wfs2json/json/data.json");
  $data = json_decode($file_content, true);

  $index = []; # assiziatives Array mit index wörtern

  foreach($data AS $set) {
    foreach($set AS $key => $value) {
      if (in_array($key, $attributes)) {
        $words = explode(' ', $value);
        foreach($words AS $word) {
          $word = trim(strtolower($word), " .,:\"()-");
          if ($word != '' && strlen($word) > 2 && !in_array($word, $stopWords)) {
            if (array_key_exists($word, $index)) {
              if (!in_array($set['id'], $index[$word])) {
                array_push($index[$word], $set['id']);
              }
            }
            else {
              $index[$word] = array($set['id']);
            }
          }
        }
      }
    }
  }

  ksort($index);

  foreach($index AS $word => $values) {
    echo '<br><b>' . $word . ':</b> ' . implode(', ', $values);
  }
  $json_text = json_encode($index);
  file_put_contents(
    getcwd() .
    '/' .
    $config['json']['localPath'] .
    $config['json']['fileName'],
    $json_text
  );
?>
</body>
</html>
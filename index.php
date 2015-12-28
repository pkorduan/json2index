<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>JSON to Index Converter</title>
</head>
<body>
<h1>Hello, welcome to the JSON to Index Converter.</h1>
<?php
  $config = parse_ini_file('config.ini', true);
  ?>
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
  $attributes = array_map(
    $remove_newline,
    file('attributes.txt', true)
  );

  # load config file with stopwords
  $stopWords = array_map(
    $remove_newline,
    file('stopwords.txt', true)
  );

  ?><h2>Convert and Save JSON</h2>
  Read JSON File from <?php echo getcwd() . '/' . $config['json']['source']; ?>,<br>
  and save the resulting Index File in <?php echo getcwd() . '/' . $config['index']['localPath'] . $config['index']['fileName']; ?><br><?php
  
  # load data file
  $file_content = file_get_contents($config['json']['source']);
  $data = json_decode($file_content, true);

  $index = []; # assiziatives Array mit index wörtern

  foreach($data AS $set) {
    foreach($set AS $key => $value) {
      if (in_array($key, $attributes)) {
        $words = explode(' ', $value);
        foreach($words AS $word) {
          $word = trim($word, " .,:\"()-");
          if ($word != '' && strlen($word) > 2 && !in_array(strtolower($word), $stopWords)) {
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
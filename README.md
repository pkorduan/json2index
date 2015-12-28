# json2index
Convert simple structured data from a JSON file to an easy searchable hash index.

Load a json data file, location configured in config.ini.
Loop through all features in the data file.
Loop through all attributes given in configuration file attribute.txt,
Explode the text in the attributes by space.
Loop through theses words and
 - trim unusual characters from the word, like .,"() and so on,
 - if they are not in the configuration file stopwords.txt add the words to the index and
   add the id of the feature to the ids array of the index if not exists already
Convert the index to JSON and
save it in a file, location configured in config.ini.

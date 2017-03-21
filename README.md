# json2index
Convert simple structured data from a JSON file to an easy searchable hash index.

Load a json data file, location configured in config ini file.
Loop through all features in the data file.
Loop through all attributes given in configuration file,
Explode the text in the attributes by space.
Loop through theses words and
 - trim unusual characters from the word, like .,"/() and so on,
 - if they are not in the configuration file stopwords.txt add the words to the index and
   add the id of the feature to the ids array of the index if not exists already
Convert the index to JSON and save it in a file, location configured in config.ini.

## Configuration Options
Copy the config sample file from config/samples/contig.ini to a self-named ini file in directory conf.
The config files must have extension ini.

Section [json]
 - source ... Location of the JSON file
 - identifier ... Name of the attribute that have unique identifier of the features in the JSON file 
 - attributes[] ... Name of the first attribut to be searched for words in the index
 - attributes[] ... Name of the second attribut to be searched for words in the index
 - attributes[] ... Add more attribute names to be searched for words

Section [index]
 - localPath ... Local path to the directory the index file should be saved
 - webPath ... Web path to the directory the index file should be saved
 - fileName ... Name of the index file
 - caseSensitive ... true if the words should be handeld in index file caseSensitive, false if not
  
## Call the app
To create the index, call your Address following with the parameter c=yourconfigfilename e.g.

http://your-server.de/json2index?c=myconfigfile.ini

<?

// Do not add trailing slash '/'
$directory = ".";

// This key must match the key spesified in your flash recorder ez publish object.
// If they don't match no files will be returned. 

// IMPORTANT: Change the key (here and in your flash recorder object) from the 
// default key if you do not want people to be able to retreive the file list.
$key = "ThisIsTheDefaultKeyChangeMe";
$limit = 10;

if ( isset( $_GET["key"] ) === false || $_GET['key'] != $key )
    return;

$files = findFiles( $directory );

// Output the list one file pr line
$counter = 0;
foreach( $files as $file )
{
    if ( $counter < $limit )
    {
        echo $file . "\n";
    }
    else
    {
        break;
    }

    $counter++;
}

// Find all files in $directory with $fileExtension as their extension.
// This function will not look for files recursively. 

// Returns an array of filenames sorted by mtime (modified), newest first.
function findFiles( $directory, $fileExtension = '.flv' )
{
    $fileArray = array();
    foreach ( glob( "$directory/*$fileExtension" ) as $filename ) 
    {
        $stat = stat( $filename );
        $name = trim( $filename, "./" );
        $fileArray[$name] = $stat['mtime'];
    }        
    arsort( $fileArray );
    return array_keys( $fileArray );
}

?>

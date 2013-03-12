<?php 

require_once 'ezc/Base/base.php';

function __autoload( $className )
{
    try
    {
        ezcBase::autoload( $className );
    }
    catch ( Exception $e )
    {
        echo $e->getMessage();
    }
}

function recursiveList( $dir, $path, &$fileList )
{
    if ( $handle = @opendir( $dir ) )
    {
        while ( ( $file = readdir( $handle ) ) !== false )
        {
            if ( ( $file == '.' ) || ( $file == '..' ) )
            {
                continue;
            }
            if ( is_dir( $dir . '/' . $file ) )
            {
                $fileList[] = array( 'path' => $path, 'name' => $file, 'type' => 'dir' );
                recursiveList( $dir . '/' . $file, $path . '/' . $file, $fileList );
            }
            else
            {
                $fileList[] = array( 'path' => $path, 'name' => $file, 'type' => 'file'  );
            }
        }
        @closedir( $handle );
    }
}

$input = new ezcConsoleInput();
$output = new ezcConsoleOutput();

$helpOption = $input->registerOption( new ezcConsoleOption( 'h', 'help' ) );
$helpOption->isHelpOption = true;
$helpOption->shorthelp = 'Display current help information.';


$extensionOption = $input->registerOption( new ezcConsoleOption( 'e', 'extension', ezcConsoleInput::TYPE_STRING ) );
$extensionOption->mandatory = true;
$extensionOption->shorthelp = 'Full path to the eZ Publish extension e.g \'/home/ls/public_html/ezp/extension/myextension\'';

$extensionNameOption = $input->registerOption( new ezcConsoleOption( 'n', 'extension-name', ezcConsoleInput::TYPE_STRING ) );
$extensionNameOption->mandatory = true;
$extensionNameOption->shorthelp = 'Extension name. e.g \'myextension\'';

try
{
    $input->process();
}
catch ( ezcConsoleOptionException $e )
{
    $output->outputLine( $e->getMessage() );
    exit();
}

if ( $helpOption->value === true )
{
    $output->outputText( $input->getHelpText( 'This script generate an XML definition for eZ Publish extension package type.' ) );
    exit();
}

$sourceDir = $extensionOption->value;
$extensionName = $extensionNameOption->value;
$fileList = array();

recursiveList( $sourceDir, '', $fileList );

$doc = new DOMDocument( '1.0', 'utf-8' );
$doc->formatOutput = true;

$packageRoot = $doc->createElement( 'extension' );
$packageRoot->setAttribute( 'name', $extensionName );

foreach( $fileList as $file )
{
    $fileNode = $doc->createElement( 'file' );
    $fileNode->setAttribute( 'name', $file['name'] );

    if ( $file['path'] )
        $fileNode->setAttribute( 'path', $file['path'] );

    $fullPath = $sourceDir . DIRECTORY_SEPARATOR . $file['path'] . DIRECTORY_SEPARATOR . $file['name'];
    $fileNode->setAttribute( 'md5sum', md5_file( $fullPath ) );

    if ( $file['type'] == 'dir' )
         $fileNode->setAttribute( 'type', 'dir' );

    $packageRoot->appendChild( $fileNode );
}

$doc->appendChild( $packageRoot );

echo $doc->saveXML();

?>
<?php
/**
 * Uploads a folder to a remote folder via FTP protocol recursively
 * Adapted
 * User: tholanger
 * git:
 * Date: 24.10.2018
 * Time: 17:25
 */

class FTPUpload
{

    private $objConnection;

    public function __construct($strServer, $strUsername, $strPasswort) {

        $this->objConnection = ftp_connect($strServer) or die("Not able to connect to server.". PHP_EOL);

        if(
            !ftp_login($this->objConnection, $strUsername, $strPasswort)
        ) {
            echo "FTP Connection failed! Close Application.";
            exit;
        }

        echo "Connected with user " . $strUsername . PHP_EOL;
        echo "Current directory: " . ftp_pwd($this->objConnection) . PHP_EOL;


    }

    public function __destruct()
    {

        if( is_resource($this->objConnection) ) if( ftp_close($this->objConnection) === true ) {
            echo "Connection closed" . PHP_EOL;
        } else {
            echo "Error closing connection";
        }


    }

    public function Upload($strLocalDir, $strRemoteDir) {

        $dirLocal = opendir($strLocalDir);

        while( FALSE !== ($filFile = readdir($dirLocal)) ) {
            if($filFile != "." && $filFile != "..") {
                if(is_dir($strLocalDir."/".$filFile)) {

                    if(!ftp_chdir($this->objConnection, $strRemoteDir."/".$filFile)) {
                        ftp_mkdir($this->objConnection, $strRemoteDir."/".$filFile);
                    }
                    $this->Upload($strLocalDir."/".$filFile, $strRemoteDir."/".$filFile);
                } else {
                    ftp_put($this->objConnection, $strRemoteDir . "/" . $filFile, $strLocalDir . "/" . $filFile, FTP_BINARY);
                }
            }
        }

    }

}

// Create an instance of Class FTPUpload
// As parameters use the credentials
$objUpload = new FTPUpload("SERVER", "USERNAME", "PASSWORD");

// Uploads the Files, please change LocalDir and Remotedir to your needs
$objUpload->Upload("./Test", "/Test");
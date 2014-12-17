<?php
  class RotatePicture extends Omeka_File_Derivative_Creator
  {
    const IMAGEMAGICK_MOGRIFY_COMMAND = 'mogrify';
    private $_mogrifyPath; // path to the ImageMagick convert binary
    
    function __construct($imDirPath)
    {
			// We are going to use a file derivator using the ExternalImageMagickStrategy
			$strategy = new Omeka_File_Derivative_Strategy_ExternalImageMagick();
			$this->setStrategy($strategy);

      // We add the mogrify path, it'll allow us to overwrite file
      if (($imDirPathClean = realpath($imDirPath)) && is_dir($imDirPath)) {
            $imDirPathClean = rtrim($imDirPathClean, DIRECTORY_SEPARATOR);
            $this->_mogrifyPath = $imDirPathClean . DIRECTORY_SEPARATOR . self::IMAGEMAGICK_MOGRIFY_COMMAND;
      } else {
        throw new Omeka_File_Derivative_Exception('ImageMagick is not properly configured: invalid directory given for the ImageMagick command!');
      }
    }


    /**
     * This function rotates the original file. 
    */    
    public function rotate($filepath, $direction)
    {
      if ($direction == "left")
      {
        $angle = "-90";
      }
      elseif ($direction == "right")
      {
        $angle = "90";
      }

      $cmd = join(' ', array(
      escapeshellcmd($this->_mogrifyPath),
      "-rotate $angle",
      escapeshellarg($filepath . '[0]'), // first page of multi-page images.
      ));
      $this->getStrategy()->executeCommand($cmd, $status, $output, $errors);
      
      if ($status) {
          throw new Omeka_File_Derivative_Exception("ImageMagick failed with status code $status. Error output:\n$errors");
      }
      if (!empty($errors)) {
          _log("Error output from ImageMagick:\n$errors", Zend_Log::WARN);
      }
      if ($status) {
      throw new Omeka_File_Derivative_Exception("ImageMagick failed with status code $status. Error output:\n$errors");
      }
      if (!empty($errors)) {
      _log("Error output from ImageMagick:\n$errors", Zend_Log::WARN);
      }
    }
  }
?>

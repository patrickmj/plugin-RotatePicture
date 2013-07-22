<?php
/**
 * Rotate Picture
 * 
 * @copyright Copyright 2013 Sylvain Machefert, Bordeaux 3
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package RotatePicture
 */

/**
 * Controller for rotation of pictures
 *
 * @package RotatePicture
 */
class RotatePicture_IndexController extends Omeka_Controller_AbstractActionController
{
  protected $_validDerivativeTypes = array('fullsize', 'thumbnail', 'square_thumbnail');
  protected $_storage;
  
  public function indexAction()
  {
    $file_id    = $this->getRequest()->getParam("file_id");
    $direction  = $this->getRequest()->getParam("direction");
    $file       = get_record_by_id("File", $file_id);
    $item_id    = metadata($file, "item_id");
    
    if (!$pathToConvert = get_option('path_to_convert')) {
        throw new Omeka_File_Derivative_Exception(__('The ImageMagick directory path is missing.'));
    }
    $imageRotator = new RotatePicture ($pathToConvert);
    $imageRotator->rotate(FILES_DIR . '/' . $file->getStoragePath("original"), $direction);
    
    // We have rotated the original file, we're going to generate all other sizes
    foreach ($this->_validDerivativeTypes as $type)
    {
      $square = false;
      if ($type == "square_thumbnail") { $square = true; }
      $imageRotator->addDerivative($type, get_option($type.'_constraint'), $square);
    }
    
    $imageCreated = $imageRotator->create(
      FILES_DIR . '/' . $file->getStoragePath('original'), 
      $file->getDerivativeFilename(), 
      $file->mime_type
    );
    
    $this->_storage = Zend_Registry::get('storage');
    
    foreach ($this->_validDerivativeTypes as $type) {
      $this->_storage->delete($file->getStoragePath($type));
      $source = FILES_DIR . "/original/{$type}_" . $file->getDerivativeFilename();
      $this->_storage->store($source, $file->getStoragePath($type));
    }
    $this->_helper->redirector->gotoRoute(array('controller'=>'files', 'action'=>'show', 'id'=>$file_id), 'id');
  }
}
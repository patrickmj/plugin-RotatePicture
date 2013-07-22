<?php
/**
 * Rotate Picture
 * 
 * @copyright Copyright 2013 Sylvain Machefert, Bordeaux 3
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Rotate Picture Plugin
 * 
 * @package Omeka\Plugins\RotatePicture
 */
class RotatePicturePlugin extends Omeka_Plugin_AbstractPlugin
{
  /**
   * @var array This plugin's hooks.
   */
  protected $_hooks = array(
    'install',
    'define_acl',
    'admin_files_form'
  );

  /**
   * Install this plugin.
   */
  public function hookInstall()
  {
      // Must be using the filesystem storage adapter.
      if (!(Zend_Registry::get('storage')->getAdapter() instanceof Omeka_Storage_Adapter_Filesystem)) {
          throw new Omeka_Plugin_Installer_Exception(__('The storage adapter is not an instance of Omeka_Storage_Adapter_Filesystem.'));
      }
      
      // The ImageMagick directory path must be set.
      if (!get_option('path_to_convert')) {
          throw new Omeka_Plugin_Installer_Exception(__('The ImageMagick directory path is missing.'));
      }
  }
  
  /**
   * Allow access only to super users.
   */
  public function hookDefineAcl($args)
  {
      // Pas sûr que tout cela ne soit nécessaire
      $args['acl']->addResource('RotatePicture_Index');
      $args['acl']->deny('admin', 'RotatePicture_Index');
  }
  
  public function hookAdminFilesForm()
  {
    print "<h2>Rotation</h2>";
    print "<a href='".url("rotate-picture?file_id=".metadata("File", "id")."&direction=left")."' class='big button'>Rotate left</a>";
    print "<a href='".url("rotate-picture?file_id=".metadata("File", "id")."&direction=right")."' class='big button'>Rotate right</a>";
  }
}
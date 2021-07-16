<?php

namespace Drupal\config_import_single\Commands;

use Drupal\config\StorageReplaceDataWrapper;
use Drupal\Core\Config\CachedStorage;
use Drupal\Core\Config\ConfigImporter;
use Drupal\Core\Config\StorageComparer;
use Drush\Commands\DrushCommands;
use Symfony\Component\Yaml\Parser;
use Webmozart\PathUtil\Path;

/**
 * Class to import single files into config.
 *
 * @package Drupal\config_import_single\Commands
 */
class ConfigImportSingleCommands extends DrushCommands {


  /**
   * CachedStorage.
   *
   * @var \Drupal\Core\Config\CachedStorage
   *   CachedStorage.
   */
  private $storage;

  /**
   * ConfigImportSingleCommands constructor.
   *
   * @param \Drupal\Core\Config\CachedStorage $storage
   *   CachedStorage.
   */
  public function __construct(CachedStorage $storage) {
    parent::__construct();
    $this->storage = $storage;
  }

  /**
   * Import a single configuration file.
   *
   * (copied from drupal console, which isn't D9 ready yet)
   *
   * @param string $file
   *   The path to the file to import.
   *
   * @command config_import_single:single-import
   *
   * @usage config_import_single:single-import <file>
   *
   * @validate-module-enabled config_import_single
   *
   * @aliases cis
   *
   * @throws \Exception
   */
  public function singleImport(string $file) {
    if (!$file) {
      throw new \Exception("No file specified.");
    }

    if (!file_exists($file)) {
      throw new \Exception("File not found.");
    }

    $source_storage = new StorageReplaceDataWrapper(
      $this->storage
    );

    $name = Path::getFilenameWithoutExtension($file);
    $ymlFile = new Parser();
    $value = $ymlFile->parse(file_get_contents($file));
    $source_storage->delete($name);
    $source_storage->write($name, $value);

    $storageComparer = new StorageComparer(
      $source_storage,
      $this->storage
    );

    if ($this->configImport($storageComparer)) {
      $this->output()->writeln("Successfully imported $name");
    }
    else {
      throw new \Exception("Failed importing file");
    }
  }

  /**
   * Import the config.
   *
   * @param \Drupal\Core\Config\StorageComparer $storageComparer
   *   The storage comparer.
   *
   * @return bool|void
   *   Returns TRUE if succeeded.
   */
  private function configImport(StorageComparer $storageComparer) {
    $configImporter = new ConfigImporter(
      $storageComparer,
      \Drupal::service('event_dispatcher'),
      \Drupal::service('config.manager'),
      \Drupal::lock(),
      \Drupal::service('config.typed'),
      \Drupal::moduleHandler(),
      \Drupal::service('module_installer'),
      \Drupal::service('theme_handler'),
      \Drupal::service('string_translation'),
      \Drupal::service('extension.list.module')
    );

    if ($configImporter->alreadyImporting()) {
      $this->output()->writeln('Import already running.');
    }
    else {
      if ($configImporter->validate()) {
        $sync_steps = $configImporter->initialize();

        foreach ($sync_steps as $step) {
          $context = [];
          do {
            $configImporter->doSyncStep($step, $context);
          } while ($context['finished'] < 1);
        }
        return TRUE;
      }
    }
  }

}

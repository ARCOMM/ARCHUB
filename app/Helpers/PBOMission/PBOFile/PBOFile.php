<?php
  namespace App\Helpers\PBOMission\PBOFile;

  use App\Helpers\PBOMission\PBOMissionHelper;

  // Header entry ending offset
  define('HEADER_ENTRY_END_OFFSET', 21);

  class PBOHeaderEntry {
    public $filename;
    public $packingMethod;
    public $originalSize;
    public $reserved;
    public $timestamp;
    public $dataSize;
    public $length;
    public $dataOffset;

    function __construct(string $filename, array $entryParams) {
      $this->filename = $filename;
      $this->packingMethod = $entryParams[0];
      $this->originalSize = $entryParams[1];
      $this->reserved = $entryParams[2];
      $this->timestamp = $entryParams[3];
      $this->dataSize = $entryParams[4];
      $this->length = strlen($this->filename) + HEADER_ENTRY_END_OFFSET;
    }

    public function setDataOffset(int $offset) {
      $this->dataOffset = $offset;
    }
  }

  class PBOHeader {
    public $entries = array();
    public $length = 0;
    private $currentDataOffset = 0;

    public function addEntry(PBOHeaderEntry $entry) {
      // Set offset for reading this entry data
      $entry->dataOffset = $this->currentDataOffset;

      // Update data offset for next entry
      $this->currentDataOffset += $entry->dataSize;

      // Update header length
      $this->length += $entry->length;

      // Add entry
      $this->entries[$entry->filename] = $entry;
    }
  }

  class PBOFile {
    public $name;
    public $error = false;
    public $errorReason;

    private $header;
    private $filepath;

    public $info;

    private static $errorReasons = array(
      'UNREADABLE_FILE' => 'Error while reading PBO file. Check whether your file is binarized or saved in a compressed format.',
    );

    function __construct(string $filepath, string $filename) {
      $this->filepath = $filepath;
      $this->name = basename($filename, '.pbo');
      $this->header = new PBOHeader();

      $pboContent = file_get_contents($filepath);

      while ($headerEntry = $this->getHeaderEntry($pboContent, $this->header->length)) {
          $this->header->addEntry($headerEntry);
      }

      if (count($this->header->entries) == 0) {
        $this->error = true;
        $this->errorReason = self::$errorReasons['UNREADABLE_FILE'];
      }

      // Calc pbo info
      $this->info = array(
        'name' => $filename,
        'size' => PBOMissionHelper::getReadableSize(filesize($filepath)),
        'files' => array_map(function($entry) {
          return array(
            'path' => $entry->filename,
            'size' => PBOMissionHelper::getReadableSize($entry->dataSize),
            'timestamp' => date('Y-m-d H:i:s',$entry->timestamp)
          );}, $this->header->entries)
      );
    }

    private function getHeaderEntry(string $pboContent, int $offset = 0): ?PboHeaderEntry {
        $entryData = unpack('Z*', $pboContent, $offset);

        if (!isset($entryData[1]) || $entryData[1] == "") {
          // Some pbo files start with empty entry, try reading next
          if ($offset == 0) {
            $this->header->length = HEADER_ENTRY_END_OFFSET + 1;
            return $this->getHeaderEntry($pboContent, $this->header->length);
          }

          return null;
        }

        $filename = $entryData[1];
        return new PboHeaderEntry($filename, array_values(unpack('L5', $pboContent, $offset + strlen($filename) + 1)));
    }

    public function getFileContent(string $filename): ?string {
      if (!isset($this->header->entries[$filename])) return null;

      $pboContent = file_get_contents($this->filepath);

      $entry = $this->header->entries[$filename];
      $offset = $this->header->length + HEADER_ENTRY_END_OFFSET + $entry->dataOffset;

      return substr($pboContent, $offset, $entry->dataSize);
    }
  }
?>

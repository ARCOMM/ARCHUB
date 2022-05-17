<?php
namespace App\Helpers\PBOMission\Mission;

define('SQM_READ_MODE_CLASS', 0);
define('SQM_READ_MODE_ATTRIBUTE', 1);

class SQMAttribute  {
  public $name;
  public $isArray;
  public $value;

  function __construct(string $name, bool $isArray) {
    $this->name = $name;
    $this->isArray = $isArray;
    $this->value = $isArray ? array() : '';
  }
}

class SQMClass {
  public $name;
  public $classes = array();
  public $attributes = array();
  public $parentClass;

  function __construct(string $name, ?SQMClass $parentClass) {
    $this->name = $name;
    $this->parentClass = $parentClass;
  }

  public function hasClass(string $key): bool {
    return isset($this->classes[$key]);
  }

  public function hasClassPath(): bool {
    $arguments = func_get_args();
    $currentClass = $this;
    foreach ($arguments as $key) {
      if (!$currentClass->hasClass($key)) return false;
      $currentClass = $currentClass->classes[$key];
    }
    return true;
  }

  public function class(string $key): ?SQMClass {
    if (!$this->hasClass($key)) return null;
    return $this->classes[$key];
  }

  public function hasAttribute(string $key): bool {
    return isset($this->attributes[$key]);
  }

  public function hasAttributes(): bool {
    $arguments = func_get_args();
    foreach ($arguments as $key) {
      if (!$this->hasAttribute($key)) return false;
    }
    return true;
  }

  public function attribute(string $key, $default = null) {
    if (!$this->hasAttribute($key)) return $default;
    return $this->attributes[$key]->value;
  }
}

class SQMConfig {
  public $root;
  private $lines;
  private $index = 0;
  private $mode;
  private $currentClass;
  private $currentAttribute;
  private $classes = array();

  public $error = false;
  public $errorReason;

  private static $errorReasons = array(
    'STRUCTURE_ERROR' => 'Syntax error in mission file (mission.sqm).',
    'BINARIZED_FILE' => 'Error while reading file (mission.sqm). Most likely your file is binarized.',
  );

  function __construct($data) {
    // Check is file readable
    if (mb_ord($data[0]) == 0) {
      $this->error = true;
      $this->errorReason = self::$errorReasons['BINARIZED_FILE'];
      return;
    }

    $this->lines = array_map('trim', preg_split("/[\r\n]+/", $data));
    $this->root = new SQMClass('_root_', null);
    $this->classes[] = $this->root;
    $this->currentClass = $this->root;
    $this->mode = SQM_READ_MODE_CLASS;

    // Parse config
    while (!$this->error && array_key_exists($this->index, $this->lines)) {
      $this->parseLine($this->lines[$this->index]);
      $this->index++;
    }

    if ($this->error) return;

    // Check if parser not ended on root (becouse it always shoud)
    if ($this->currentClass != $this->root) {
      $this->error = true;
      $this->errorReason = self::$errorReasons['STRUCTURE_ERROR'];
      return;
    }

    // Cleanup temporary data & references
    foreach ($this->classes as $class) unset($class->parentClass);

    unset($this->classes);
    unset($this->currentClass);
    unset($this->currentAttribute);
    unset($this->lines);
    unset($this->index);
  }

  private function parseLine($line) {
    //Filter out empty lines, comments and config defines
    if ($line == '' || $line[0] == '/' || $line[0] == '#') return;
    // Remove semicolon ending
    if ($line[-1] == ';') $line = substr($line, 0, -1);

    // Read end of class/attribute
    if ($line == '}') {
      if ($this->mode == "class" || $this->mode == SQM_READ_MODE_CLASS) {
        // Check if we are within structure
        if ($this->currentClass->parentClass == null) {
          $this->error = true;
          $this->errorReason = self::$errorReasons['STRUCTURE_ERROR'];
          return;
        }

        $this->currentClass = $this->currentClass->parentClass;
      }

      $this->mode = SQM_READ_MODE_CLASS;
      return;
    }

    // Read attribute value
    if ($this->mode == SQM_READ_MODE_ATTRIBUTE) {
      // Check for safety is attr an array, should be in 100% of cases
      if ($this->currentAttribute->isArray) {
        if ($line[-1] == ',') $line = substr($line, 0, -1);
        $this->currentAttribute->value[] = trim($line, '"');
      } else {
        $this->currentAttribute->value = trim($line, '"');
      }

      return;
    }

    // Read new class
    if (substr($line, 0, 6) == 'class ') {
      $this->mode = 'class';
      $class = new SQMClass(substr($line, 6), $this->currentClass);
      $this->currentClass->classes[$class->name] = $class;
      $this->currentClass = $class;
      $this->classes[] = $class;

      // Skip class entry line
      if ($this->lines[$this->index + 1] == '{') $this->index += 1;

      return;
    }

    // Read attribute
    if (strpos($line, '=') !== false) {
      $attributeArray = explode('=', $line, 2);

      $isMultiLineArray = $attributeArray[1] == '' && $this->lines[$this->index + 1] == '{';
      $isArray = $isMultiLineArray || $attributeArray[1][0] == '{';

      $attribute = new SQMAttribute($attributeArray[0], $isArray);

      if ($isMultiLineArray) {
        $this->mode = SQM_READ_MODE_ATTRIBUTE;
        $this->currentAttribute = $attribute;
        // Skip array entry line
        $this->index += 1;
      } else {
        $value = $attributeArray[1];
        if ($isArray) {
          $value = explode(',', trim($value, '{}'));
        } else {
          $value = trim($value, '"');
        }

        $attribute->value = $value;
      }

      $this->currentClass->attributes[$attribute->name] = $attribute;

      return;
    }
  }
}

?>

<?php
   // Compression d'un fichier ou d'une chaine en BZIP2
   // -------------------
   // -- Instanciation --
   // -------------------
   // $obj = new Bzip2($inputType, $inputData, $outputType, $outputFile);
   // $inputType : FILE | VARIABLE
   // $inputData : si $inputType = FILE : $inputData contient le chemin complet du fichier à compresser. si $inputType = VARIABLE : $inputData contient la chaîne à compresser
   // $outputType : FILE | VARIABLE
   // $outputFile : si $outputType = FILE : $outputFile contient le chemin complet du fichier de sortie
   
   namespace processid\compress;
   
   class Bzip2 {
      protected $_inputType;
      protected $_inputData;
      protected $_inputFilePointer;
      protected $_outputType;
      protected $_outputFile;
      protected $_outputFilePointer;
      protected $_outputData;
      protected $_compressionLevel;
      
      // Constantes de $_inputType et $_outputType
      const FILE = 1;
      const VARIABLE = 2;
      
      function __construct($inputType, $inputData, $outputType, $outputFile = '') {
         $this->inputData = $inputData;
         $this->setInputType($inputType);
         $this->outputFile = $outputFile;
         $this->setOutputType($outputType);
         $this->outputData = '';
         $this->setCompressionLevel(4);
      }
      
      function setCompressionLevel($compression_level) {
         if (is_int($compression_level)) {
            if ($compression_level < 1) {
               $compression_level = 1;
            }
            if ($compression_level > 9) {
               $compression_level = 9;
            }
            $this->compressionLevel = $compression_level;
         } else {
            trigger_error('Le niveau de compression doit etre un entier entre 1 et 9 (9 est la meilleure compression)', E_USER_ERROR);
         }
      }
      
      function setInputType($inputType) {
         if (is_int($inputType)) {
            if ($inputType == self::FILE || $inputType == self::VARIABLE) {
               $this->inputType = $inputType;
               
               if ($this->inputType == self::FILE) {
                  if (!strlen($this->inputData)) {
                     trigger_error('Vous devez preciser le fichier d\'entree', E_USER_ERROR);
                  } else {
                     $this->inputFilePointer = fopen($this->inputData,'rb');
                     if (!$this->inputFilePointer) {
                        trigger_error('Impossible d\'ouvrir le fichier d\'entree : ' . $this->inputData, E_USER_ERROR);
                     }
                  }
               }
            } else {
               trigger_error('La methode d\'entree doit etre FILE ou VARIABLE', E_USER_ERROR);
            }
         } else {
            trigger_error('La methode d\'entree doit etre FILE ou VARIABLE', E_USER_ERROR);
         }
      }
      
      function setOutputType($outputType) {
         if (is_int($outputType)) {
            if ($outputType == self::FILE || $inputType == self::VARIABLE) {
               $this->outputType = $outputType;
               
               if ($this->outputType == self::FILE) {
                  if (!strlen($this->outputFile)) {
                     trigger_error('Vous devez preciser le fichier de sortie avec la methode de sortie FILE', E_USER_ERROR);
                  } else {
                     $this->outputFilePointer = bzopen($this->outputFile,'w');
                     if (!$this->outputFilePointer) {
                        trigger_error('Impossible d\'ecrire dans le fichier : ' . $this->outputFile, E_USER_ERROR);
                     }
                  }
               }
            } else {
               trigger_error('La methode de sortie doit être FILE ou VARIABLE', E_USER_ERROR);
            }
         } else {
            trigger_error('La methode de sortie doit être FILE ou VARIABLE', E_USER_ERROR);
         }
      }
      
      function compress() {
         if ($this->inputType == self::FILE) {
            while (!feof($this->inputFilePointer)) {
               $str = fread($this->inputFilePointer, 100000*$this->compressionLevel);
               if ($this->outputType == self::FILE) {
                  bzwrite($this->outputFilePointer, $str);
               } else {
                  $this->outputData .= bzcompress($str,$this->compressionLevel);
               }
            }
            fclose ($this->inputFilePointer);
         } else {
            if ($this->outputType == self::FILE) {
               bzwrite($this->outputFilePointer, $this->inputData);
            } else {
               $this->outputData .= bzcompress($this->inputData,$this->compressionLevel);
            }
         }
         if ($this->outputType == self::FILE) {
            bzflush($this->outputFilePointer);
            bzclose($this->outputFilePointer);
            return true;
         } else {
            return $this->outputData;
         }
      }
      
   }
?>

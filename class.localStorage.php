<?php

class localStorage {
    
    private $storageFile;
    private $data;
    
    public function __construct($fileName) {
        $this->storageFile = "cache/".$fileName.".local";
        if (!file_exists($fileName)) {
            $handle = fopen($fileName, 'w') or die("Cannot create file: ".$fileName);
            fclose($handle);
        }
        $this->data = json_decode(file_get_contents($fileName), true) ?? [];
    }
    
    public function setItem($key, $value) {
        $this->data[$key] = $value;
        $this->saveData();
    }
    
    public function getItem($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    
    public function removeItem($key) {
        unset($this->data[$key]);
        $this->saveData();
    }
    
    private function saveData() {
        $json = json_encode($this->data);
        file_put_contents($this->storageFile, $json);
    }
    
}

?>
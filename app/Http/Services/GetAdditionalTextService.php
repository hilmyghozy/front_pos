<?php

namespace App\Http\Services;

class GetAdditionalTextService
{
    /**
     * @var string $text
     */
    public $text;

    /**
     * @var null|object
     */
    public $item_type;

    /**
     * @var null|object
     */
    public $item_size;

    /**
     * @param string $text
     * @param null|object $item_type
     * @param null|object $item_size
     */
    public function __construct ($text, $item_type, $item_size)
    {
        $this->text = $text;
        $this->item_type = $item_type;
        $this->item_size = $item_size;
    }

    public function __toString()
    {
        $item_type = $this->item_type;
        if ($item_type) {
            if (is_string($item_type)) $item_type = json_decode($item_type);
            if (is_object($item_type)) $item_type = (array)$item_type;
            if (isset($item_type['text'])) {
                $this->text = $item_type['text'];
            } else if (isset($item_type['nama_type'])) {
                $nama_type = $item_type['nama_type'];
                $contain_type = str_contains($this->text, "($nama_type)");
                if ($contain_type) {
                    return $this->text;
                } else {
                    return $this->text . " " . $nama_type;
                }
            } else {
                $this->text = $this->text;
            }
        }
        // $additional_text = '';
        // if ($this->item_type) {
        //     $text = $this->item_type['text'];
        //     $additional_text = "$additional_text $text ";
        // }
        // if ($this->item_size) {
        //     $text = $this->item_size['text'];
        //     $additional_text = "$additional_text $text ";
        // }
        // if ($this->item_size || $this->item_type) {
        //     $text = $this->text;
        //     $this->text = "$text ($additional_text)";
        // }
        return $this->text;
    }
}
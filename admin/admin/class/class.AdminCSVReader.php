<?php
########################################################################
# ******************  SX CONTENT MANAGEMENT SYSTEM  ****************** #
# *       Copyright © Alexander Voloshin * All Rights Reserved       * #
# ******************************************************************** #
# *  http://sx-cms.ru   *  cms@sx-cms.ru  *   http://www.status-x.ru * #
# ******************************************************************** #
########################################################################
if (!defined('SX_DIR')) {
    header('Refresh: 0; url=/index.php?p=notfound', true, 404); exit;
}

class AdminCSVReader {

    public $_fp;
    public $_fields;
    public $_rows;
    public $_pointer;
    public $_lf;

    public function __construct($fp) {
        $this->_fp = $fp;
        $this->_pointer = 0;
        $this->_rows = array();
        $content = '';
        while (!feof($this->_fp)) {
            $content .= fread($fp, 8096);
        }
        if (strpos($content, "\r\n") !== false) {
            ($this->_lf = "\n") && ($content = str_replace("\r", '', $content));
        } elseif (strpos($content, "\n") !== false) {
            $this->_lf = "\n";
        } elseif (strpos($content, "\r") !== false) {
            $this->_lf = "\r";
        } else {
            $this->_lf = "\n";
        }
        $this->_rows = $this->_parse($content);
        $this->_fields = $this->_rows[0];
    }

    public function fetch() {
        if ($this->_pointer >= count($this->_rows)) {
            return (false);
        }
        $this->_pointer++;
        $row = array();
        foreach ($this->_fields as $key => $value) {
            $row[$value] = $this->_rows[$this->_pointer][$key];
        }
        return $row;
    }

    public function fields() {
        return $this->_fields;
    }

    public function count() {
        return count($this->_fields);
    }

    protected function _parse($data) {
        $rows = array();
        $rows[$row_p = 0] = array();
        $col_p = 0;
        $lastc = chr(0);
        $c = chr(0);
        $in_string = false;

        for ($i = 0; $i < strlen($data); $i++) {
            $lastc = $i == 0 ? chr(0) : $data[$i - 1];
            $c = $data[$i];

            if ($c == '"' && $lastc != '\\') {
                $in_string = !$in_string;
            } elseif (($c == ';' || $c == ',') && !$in_string) {
                $col_p++;
            } elseif (($c == $this->_lf) && !$in_string) {
                $col_p = 0;
                $rows[++$row_p] = array();
            } else {
                $rows[$row_p][$col_p] .= $c;
            }
        }
        return $rows;
    }

}

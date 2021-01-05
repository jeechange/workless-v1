<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Search;

/**
 * 搜索框助手
 *
 * @author Administrator
 */
class Search {

    private $data = array();
    private $fields = array();
    private $options = array();
    private $submited = false;
    private $buttons = array();
    private $label_type = "label";
    private $label_types = array("label", "placeholder");


    public function labelType($label_type) {
        $this->label_type = in_array($label_type, $this->label_types) ? $label_type : "label";
    }

    /**
     *
     * @var \phpex\Library\Controller
     */
    protected $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function optoins(array $options = array()) {
        $this->options = array_merge($this->options, $options);
    }


    public function getOptions() {
        return $this->options;
    }

    public function clear() {
        $this->data = array();
        $this->fields = array();
        $this->options = array();
        $this->submited = false;
    }

    public function addSubmit($label = "搜索") {
        if (!$this->submited) {
            $this->submited = true;
            $this->buttons[] = array("type" => "submit", "label" => $label);
        }
    }

    public function addExport($field = "excel", $label = "导出到excel") {
        $this->buttons[] = array("type" => "export", "name" => "__export__" . $field, "label" => $label);
    }

    public function addKeyword($field, $label = "关键词", $like = true) {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "key", "fields" => explode(",", $field), "label" => $label, 'like' => $like);
    }

    public function addChoiceKeyword($fields, $choiceName = "choice_keyword", $like = true) {
        $input_name = str_replace(array(",", "."), array("_", ""), $choiceName);
        $this->fields[] = array("name" => $input_name, "type" => "choiceKeyword", "fields" => $fields, 'like' => $like);

    }

    public function addDateRange($field, $label = "日期") {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "daterange", "field" => $field, "label" => $label);
        return $this;
    }

    public function addTimeRange($field, $label = "时间") {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "timerange", "field" => $field, "label" => $label);
    }

    public function addSelect($field, $label = "类型", array $options = array(), $empty = false, $default = false, $isnull = false) {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "select", "field" => $field, "label" => $label, "options" => $options, "empty" => $empty, "default" => $default, "isnull" => $isnull);
    }

    public function addSelectTree($field, $label = "树类型", array $options = array(), $empty = false, $default = false, $isnull = false) {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "select_tree", "field" => $field, "label" => $label, "options" => $options, "empty" => $empty, "default" => $default, "isnull" => $isnull);
    }

    public function addSelectOptions($field, $label = "类型", $options = "", $default = false) {
        $input_name = str_replace(array(",", "."), array("_", ""), $field);
        $this->fields[] = array("name" => $input_name, "type" => "select_options", "field" => $field, "label" => $label, "options" => $options, "default" => $default);
    }

    public function bindData(array $data) {
        $this->data = $data;
    }

    /**
     * 构建查询
     * @param string $where 生成的DQL
     * @param string $searchForm 生成的表单
     * @param array $parameters 生成的DQL参数
     */
    public function build(&$where = "", &$searchForm = "", &$parameters = array()) {

        $where = $where ? $where . " AND " : "";
        $this->addSubmit();
        foreach ($this->fields as $field) {
            switch ($field["type"]) {
                case "key":
                    $value = $this->likeValue($field["name"], $data);
                    if (isset($this->data[$field["name"] . "_eq"])) {
                        $value = $data;
                    }
                    if ($value) {
                        $where .= "( ";
                        $join = "";
                        $join_str = isset($this->data[$field["name"] . "_eq"]) && $field["like"] == false ? " = " : " LIKE ";
                        foreach ($field["fields"] as $f) {
                            $where .= $join . $f . $join_str . ":" . str_replace(".", "_", $f);
                            $parameters[str_replace(".", "_", $f)] = $value;
                            $join = " OR ";
                        }
                        $where .= " ) AND ";
                    }
                    if ($this->label_type == "label") {
                        $searchForm .= "&nbsp;" . $field["label"] . "：<input class='search-keyword' style=\"width:100px;\" name=\"{$field["name"]}\" type=\"text\" value=\"{$data}\" />";
                    } elseif ($this->label_type == "placeholder") {
                        $searchForm .= "&nbsp;<input class='search-keyword' style=\"width:100px;\" name=\"{$field["name"]}\" type=\"text\" value=\"{$data}\" placeholder=\"{$field["label"]}\" />";
                    }
                    if ($field["like"] == false) {
                        if (isset($this->data[$field["name"] . "_eq"])) {
                            $searchForm .= "&nbsp;<label><input name=\"{$field["name"]}_eq\" type=\"checkbox\" checked value=\"1\" />精准</label>&nbsp;&nbsp;";
                        } else {
                            $searchForm .= "&nbsp;<label><input name=\"{$field["name"]}_eq\" type=\"checkbox\" value=\"1\" />精准</label>&nbsp;&nbsp;";
                        }
                    } else {
                        $searchForm .= "";
                    }
                    break;
                case "daterange":
                case "timerange":
                    $values = $this->betweenDate($field["name"], $start, $end);
                    $params_f = str_replace(".", "_", $field["field"]);
                    if ($field["type"] == 'daterange' && $values) {
                        $vals[0] = $values[0] ? $values[0] . " 00:00:01" : "";
                        $vals[1] = $values[1] ? $values[1] . " 23:59:59" : "";
                    } else {
                        $vals = $values;
                    }
                    if (2 == count($vals)) {
                        $where .= "( " . $field["field"] . " BETWEEN :" . $params_f . "_start AND :" . $params_f . "_end) AND ";
                        $parameters[$params_f . "_start"] = $vals[0];
                        $parameters[$params_f . "_end"] = $vals[1];
                    } elseif (isset($vals[0])) {
                        $where .= "( " . $field["field"] . " >= :" . $params_f . "_start) AND ";
                        $parameters[$params_f . "_start"] = $vals[0];
                        $vals[1] = "";
                    } elseif (isset($vals[1])) {
                        $where .= "( " . $field["field"] . " <= :" . $vals[1] . "_end) AND ";
                        $parameters[$params_f . "_end"] = $vals[0];
                        $vals[0] = "";
                    } else {
                        $vals[0] = $vals[1] = "";
                    }
                    $format = $field["type"] == "daterange" ? "YYYY-MM-DD" : "YYYY-MM-DD hh:mm:ss";

                    if ($this->label_type == "label") {

                        $searchForm .= "&nbsp;" . $field["label"] . "：<input class=\"laydate laydate-icon\""
                            . " date-max=\"{$end}\"  type=\"text\"  style=\"width:130px;\" name=\"{$start}\" id=\"{$start}\" format=\"{$format}\" value=\"{$values[0]}\">";

                        $searchForm .= "&nbsp;-&nbsp;<input style=\"width:130px;\" class=\"laydate laydate-icon\""
                            . " date-min=\"{$start}\" type=\"text\" name=\"{$end}\" id=\"{$end}\" format=\"{$format}\" value=\"{$values[1]}\">";

                    } elseif ($this->label_type == "placeholder") {
                        $searchForm .= "&nbsp;<input class=\"laydate laydate-icon\""
                            . " date-max=\"{$end}\"  type=\"text\"  style=\"width:130px;\" name=\"{$start}\" id=\"{$start}\" format=\"{$format}\" value=\"{$values[0]}\" placeholder=\"开始{$field["label"]}\">";

                        $searchForm .= "&nbsp;-&nbsp;<input style=\"width:130px;\" class=\"laydate laydate-icon\""
                            . " date-min=\"{$start}\" type=\"text\" name=\"{$end}\" id=\"{$end}\" format=\"{$format}\" value=\"{$values[1]}\" placeholder=\"{$field["label"]}\">";
                    }
                    break;
                case "select":
                    $value = $this->selectOptions($field["name"], $field["options"], $field["default"]);
                    if ("" !== $value) {
                        if (false === $field["isnull"] || $field["isnull"] != $value)
                            $where .= "( " . $field["field"] . " = :" . str_replace(".", "_", $field["field"]) . ") AND ";
                        else
                            $where .= "( " . $field["field"] . " = :" . str_replace(".", "_", $field["field"]) . " OR " . $field["field"] . " IS NULL  ) AND ";
                        $parameters[str_replace(".", "_", $field["field"])] = $value;
                    }
                    if ($this->label_type == "label") {
                        $searchForm .= "&nbsp;" . $field["label"] . "：<select style=\"min-width:80px\" name=\"{$field["name"]}\">";
                    } elseif ($this->label_type == "placeholder") {
                        $searchForm .= "&nbsp;<select style=\"min-width:80px\" name=\"{$field["name"]}\" placeholder=\"{$field["label"]}\">";
                    }
                    if ($field["empty"]) {
                        $searchForm .= "<option value=\"\">{$field['empty']}</option>";
                    }
                    foreach ($field["options"] as $key => $option) {
                        $selected = (strval($value) === strval($key)) ? " selected=\"selected\" " : "";
                        $searchForm .= "<option value=\"{$key}\"{$selected}>{$option}</option>";
                    }
                    $searchForm .= "</select>";
                    break;
                case "select_tree":
                    $value = $this->selectTreeOptions($field["name"], $field["options"], $field["default"]);
                    if ("" !== $value[0]) {
                        $typeid = empty($value[1]) ? '=' : "in";
                        if (false === $field["isnull"] || $field["isnull"] != $value[0])
                            $where .= "( " . $field["field"] . " {$typeid} (:" . str_replace(".", "_", $field["field"]) . ")) AND ";
                        else
                            $where .= "( " . $field["field"] . " {$typeid} (:" . str_replace(".", "_", $field["field"]) . ") OR " . $field["field"] . " IS NULL  ) AND ";
                        $parameters[str_replace(".", "_", $field["field"])] = empty($value[1]) ? $value[0] : array_merge(array($value[0]), $value[1]);
                    }
                    if ($this->label_type == "label") {
                        $searchForm .= "&nbsp;" . $field["label"] . "：<select style=\"min-width:80px\" name=\"{$field["name"]}\">";
                    } elseif ($this->label_type == "placeholder") {
                        $searchForm .= "&nbsp;<select style=\"min-width:80px\" name=\"{$field["name"]}\" placeholder=\"{$field["label"]}\">";
                    }
                    if ($field["empty"]) {
                        $searchForm .= "<option value=\"\">{$field['empty']}</option>";
                    }
                    foreach ($field["options"] as $key => $option) {
                        $selected = (strval($value[0]) === strval($key)) ? " selected=\"selected\" " : "";
                        $searchForm .= "<option value=\"{$key}\"{$selected}>{$option[0]}</option>";
                    }
                    $searchForm .= "</select>";
                    break;
                case "select_options":
                    $value = $this->selectOptions($field["name"], $field["options"], $field["default"]);
                    if ("" !== $value) {
                        if (false === $field["isnull"] || $field["isnull"] != $value)
                            $where .= "( " . $field["field"] . " = :" . str_replace(".", "_", $field["field"]) . ") AND ";
                        else
                            $where .= "( " . $field["field"] . " = :" . str_replace(".", "_", $field["field"]) . " OR " . $field["field"] . " IS NULL  ) AND ";
                        $parameters[str_replace(".", "_", $field["field"])] = $value;
                    }

                    if ($this->label_type == "label") {
                        $searchForm .= "&nbsp;" . $field["label"] . "：<select style=\"min-width:80px\" name=\"{$field["name"]}\">";
                    } elseif ($this->label_type == "placeholder") {
                        $searchForm .= "&nbsp;<select style=\"min-width:80px\" name=\"{$field["name"]}\" placeholder=\"{$field["label"]}\">";
                    }

                    $searchForm .= $field["options"];
                    $searchForm .= "</select>";
                    break;
                case "choiceKeyword":
                    $value = $this->likeValue($field["name"] . "_val", $data);
                    if (isset($this->data[$field["name"] . "_eq"])) {
                        $value = $data;
                    }
                    $fieldName = $this->data[$field["name"] . "_name"];
                    $fields = explode(",", $fieldName);
                    if ($value) {
                        $where .= "( ";
                        $join = "";
                        $join_str = isset($this->data[$field["name"] . "_eq"]) && $field["like"] == false ? " = " : " LIKE ";
                        foreach ($fields as $f) {
                            $where .= $join . $f . $join_str . ":" . str_replace(".", "_", $f);
                            $parameters[str_replace(".", "_", $f)] = $value;
                            $join = " OR ";
                        }
                        $where .= " ) AND ";
                    }
                    $searchForm .= "&nbsp;<select style=\"min-width:80px\" name=\"{$field["name"]}_name\">";
                    if ($field["empty"]) {
                        $searchForm .= "<option value=\"\">{$field['empty']}</option>";
                    }
                    foreach ($field["fields"] as $key => $option) {
                        $selected = (strval($fieldName) === strval($key)) ? " selected=\"selected\" " : "";
                        $searchForm .= "<option value=\"{$key}\"{$selected}>{$option}</option>";
                    }
                    $searchForm .= "</select>";
                    $searchForm .= "&nbsp;<input style=\"width:100px;\" name=\"{$field["name"]}_val\" type=\"text\" value=\"{$data}\" />";
                    if ($field["like"] == false) {
                        if (isset($this->data[$field["name"] . "_eq"])) {
                            $searchForm .= "&nbsp;<label><input name=\"{$field["name"]}_eq\" type=\"checkbox\" checked value=\"1\" />精准</label>&nbsp;&nbsp;";
                        } else {
                            $searchForm .= "&nbsp;<label><input name=\"{$field["name"]}_eq\" type=\"checkbox\" value=\"1\" />精准</label>&nbsp;&nbsp;";
                        }
                    } else {
                        $searchForm .= "";
                    }
                    break;
            }
        }
        $buttonstr = "";
        foreach ($this->buttons as $button) {
            switch ($button["type"]) {
                case "submit":
                    $buttonstr = "&nbsp;<input type=\"submit\" class=\"btn min green\" value=\"{$button["label"]}\" />&nbsp;" . $buttonstr;
                    break;
                case "export":
                    $url = $_SERVER['REQUEST_URI'];
                    $p = $button["name"];
                    if (preg_match("/([\?&]$p\=).+/", $url)) {
                        $url = preg_replace("/([\?&]$p\=).+/", '\\1__PAGE__', $url);
                    } elseif (preg_match("/\?/", $url)) {
                        $url .= "&" . http_build_query(array($p => $button["label"]));
                    } else {
                        $url .= "?" . http_build_query(array($p => $button["label"]));
                    }
                    $buttonstr .= "&nbsp;<input type=\"button\" onclick=\"location.href='$url'\" class=\"btn min green\" name=\"{$button["name"]}\" value=\"{$button["label"]}\" />&nbsp;";
                    break;
            }
        }
        $searchForm = $this->header() . $searchForm . $buttonstr . $this->footer();
        $where = substr($where, 0, -5);
        $this->controller->assign("searchForm", $searchForm);
    }

    public function header() {
        $form = "<form ";
        foreach ($this->options as $option => $attr) {
            if ($option == "element" && is_scalar($attr))
                continue;
            $form .= $option . '="' . htmlspecialchars($attr, ENT_QUOTES, 'UTF-8') . '" ';
        }
        return trim($form) . ">";
    }

    public function footer() {
        return "</form>";
    }

    private function likeValue($name, &$data) {
        $data = "";
        if (isset($this->data[$name]) && !empty($this->data[$name])) {
            $data = $this->data[$name];
            return "%" . trim($this->data[$name]) . "%";
        }
        return "";
    }

    private function betweenDate($name, &$start, &$end) {
        $start = $name . "_start";
        $end = $name . "_end";
        $values = array();
        if (isset($this->data[$start]) && !empty($this->data[$start])) {
            $values[0] = trim($this->data[$start]);
        }
        if (isset($this->data[$end]) && !empty($this->data[$end])) {
            $values[1] = trim($this->data[$end]);
        }
        return $values;
    }

    private function selectOptions($name, $options, $default) {
        if (isset($this->data[$name]) && isset($options[$this->data[$name]])) {
            return $this->data[$name];
        }
        return false === $default ? "" : $default;
    }

    private function selectTreeOptions($name, $options, $default) {
        if (isset($this->data[$name]) && isset($options[$this->data[$name]])) {
            $items = $this->findTreeOptions($options, $this->data[$name]);
            return array($this->data[$name], $items);
        }
        return false === $default ? array("", array()) : $this->selectTreeOptions($default, $options, false);
    }

    private function findTreeOptions($options, $id) {
        $items = array();
        foreach ($options as $oid => $row) {
            if ($row[1] == $id) {
                $items[] = $oid;
                $items = array_merge($items, $this->findTreeOptions($options, $oid));
            }
        }
        return $items;
    }

    public function getData($key) {
        $name = str_replace(array(",", "."), array("_", ""), $key);
        foreach ($this->fields as $field) {
            if ($field["name"] != $name)
                continue;
            switch ($field["type"]) {
                case "key":
                    $this->likeValue($name, $data);
                    return $data;
                case "daterange":
                    $this->betweenDate($name, $start, $end);
                    return array($start, $end);
                case "select":
                case "select_options":
                    return $this->selectOptions($name, $field["options"], $field["default"]);
            }
        }
        return null;
    }

}

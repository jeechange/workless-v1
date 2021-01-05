<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/3/17
 * Time: 10:31
 */

namespace phpex\Helper\Search\RichElement;


class Multi extends BaseElement {

    private $multis = array();

    protected $overflow = 6;

    public function where() {
        $this->parameters = array();
        $value = $this->getValues();
        if ($value === null || $value == "-1") return "";

        $aliasValues = explode("|", $value);
        $wheres = array();
        foreach ($aliasValues as $i => $aliasValue) {
            $operator = "=";
            $realValue = "";
            foreach ($this->multis as $multi) {
                if ($aliasValue == $multi[3]) {
                    $operator = $multi[2];
                    $realValue = $multi[1];
                    break;
                }
            }
            $key = sprintf("%s_%d", $this->realKey, $i);
            if ($operator != "~") $this->parameters[$key] = $realValue;
            switch (strtolower($operator)) {
                case "like":
                    $this->parameters[$key] = '%' . $realValue . "%";
                    $wheres[] = sprintf('%s LIKE :%s', $this->elementKey, $key);
                    break;
                case "in":
                    $wheres[] = sprintf('%s IN (:%s)', $this->elementKey, $key);
                    break;
                case "~":
                    $vals = explode(',', $realValue);
                    $this->parameters[$key . '_start'] = $vals[0];
                    $this->parameters[$key . '_end'] = isset($vals[1]) ? $vals[1] : "";
                    $wheres[] = sprintf('(%s BETWEEN :%s_start and :%s_end)', $this->elementKey, $key, $key);
                    break;
                case "regexp":
                    $wheres[] = sprintf('REGEXP(%s,:%s)=1', $this->elementKey, $key);
                    break;
                case ">":
                case ">=":
                case "<":
                case "<=":
                case "<>":
                case "=":
                    $wheres[] = sprintf('%s %s :%s', $this->elementKey, $operator, $key);
                    break;
                default:
                    $wheres[] = sprintf('%s = :%s', $this->elementKey, $key);
            }
        }
        return '(' . join(" OR ", $wheres) . ')';
    }

    public function addItem($label, $values, $operator = "=", $alias = "") {
        $this->multis[] = array($label, $values, $operator, $alias ?: $values);
        return $this;
    }

    public function addItems(array $multis) {
        foreach ($multis as $multi) {
            $this->multis[] = array($multi[0], $multi[1], isset($multi[2]) ? $multi[2] : "=", isset($multi[3]) ? $multi[3] : $multi[1]);
        }
        return $this;
    }

    public function form() {
        $value = $this->getValues();
        if ($value === null) $value = -1;
        $form = sprintf('<tr class="rich-search-element element-multi"><td class="element-multi-label element-label"><em>%s</em>' .
            '<label class="all%s"><input class="input input-hide" data-key="%s" type="checkbox" value="-1">全部</label></td>',
            $this->elementLabel,
            $value == -1 ? ' selected' : '',
            $this->elementKey
        );


        $items = "";
        $selectIndex = 0;
        $count = 0;

        $aliasValues = explode("|", $value);
        foreach ($this->multis as $index => $multi) {
            $items .= sprintf('<label class="element-item element-multi-item%s%s"><input class="input input-hide" data-key="%s" type="checkbox" value="%s">%s</label>',
                in_array($multi[3], $aliasValues) ? " selected" : "",
                $index >= $this->overflow ? " element-overflow" : "",
                $this->elementKey,
                $multi[3],
                $multi[0]
            );
            if (in_array($multi[3], $aliasValues)) $selectIndex = $index;
            $count++;
        }

        $form .= sprintf('<td class="element-items element-multi-items%s">', $selectIndex < $this->overflow ? ' overflow-hide' : "");
        $form .= $items;
        if ($count > $this->overflow) {
            $form .= sprintf('<a href="javascript:void(0);" class="more">%s</a>', $selectIndex >= $this->overflow ? ' 隐藏' : "更多");
        }
        $form .= '</td></tr>';
        return $form;
    }

    public function setOverflow($overflow) {
        $this->overflow = $overflow;
        return $this;
    }
}

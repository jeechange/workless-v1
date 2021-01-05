<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/3/17
 * Time: 10:25
 */

namespace phpex\Helper\Search\RichElement;


class Choice extends BaseElement {
    private $choices = array();

    private $overflow = 6;

    public function where() {
        $this->parameters = array();
        $aliasValue = $this->getValues();

        if ($aliasValue === null || $aliasValue == "-1") return "";
        $operator = "=";
        $realValue = "";
        foreach ($this->choices as $choice) {
            if ($aliasValue == $choice[3]) {
                $operator = $choice[2];
                $realValue = $choice[1];
                break;
            }
        }
        $this->parameters[$this->realKey] = $realValue;
        switch (strtolower($operator)) {
            case "like":
                $this->parameters[$this->realKey] = '%' . $realValue . "%";
                return sprintf('%s LIKE :%s', $this->elementKey, $this->realKey);
            case "in":
                return sprintf('%s IN (:%s)', $this->elementKey, $this->realKey);
            case "~":
                $vals = explode(',', $realValue);
                $this->parameters = array(
                    $this->realKey . '_start' => $vals[0],
                    $this->realKey . '_end' => isset($vals[1]) ? $vals[1] : ""
                );
                return sprintf('(%s BETWEEN :%s_start and :%s_end)', $this->elementKey, $this->realKey, $this->realKey);
            case "regexp":
                return sprintf('REGEXP(%s,:%s)=1', $this->elementKey, $this->realKey);
            case ">":
            case ">=":
            case "<":
            case "<=":
            case "<>":
            case "=":
                return sprintf('%s %s :%s', $this->elementKey, $operator, $this->realKey);
            default:
                return sprintf('%s = :%s', $this->elementKey, $this->realKey);
        }

    }

    public function form() {
        $value = $this->getValues();
        if ($value === null) $value = -1;

        $form = sprintf('<tr class="rich-search-element element-choice"><td class="element-label element-choice-label"><em>%s</em>' .
            '<label class="all%s"><input class="input input-hide" type="checkbox" data-key="%s" value="-1">全部</label></td>',
            $this->elementLabel,
            $value == -1 ? ' selected' : '',
            $this->elementKey
        );

        $items = "";
        $selectIndex = 0;
        $count = 0;
        foreach ($this->choices as $index => $choice) {
            $items .= sprintf('<label class="element-item element-choice-item%s%s"><input class="input input-hide" data-key="%s" type="checkbox" value="%s">%s</label>',
                $choice[3] == $value ? " selected" : "",
                $index >= $this->overflow ? " element-overflow" : "",
                $this->elementKey,
                $choice[3],
                $choice[0]
            );
            if ($choice[3] == $value) $selectIndex = $index;
            $count++;
        }
        $form .= sprintf('<td class="element-items element-choice-items%s">', $selectIndex < $this->overflow ? ' overflow-hide' : "");

        $form .= $items;

        if ($count > $this->overflow) {
            $form .= sprintf('<a href="javascript:void(0);" class="more">%s</a>', $selectIndex >= $this->overflow ? ' 隐藏' : "更多");
        }

        $form .= '</td></tr>';
        return $form;
    }

    public function addItem($label, $values, $operator = "=", $alias = "") {
        $this->choices[] = array($label, $values, $operator, $alias ?: $values);
        return $this;
    }

    public function addItems(array $choices) {
        foreach ($choices as $choice) {
            $this->choices[] = array($choice[0], $choice[1], isset($choice[2]) ? $choice[2] : "=", isset($choice[3]) ? $choice[3] : $choice[1]);
        }
        return $this;
    }

    public function setOverflow($overflow) {
        $this->overflow = $overflow;
        return $this;
    }

}

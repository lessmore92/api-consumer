<?php
/**
 * User: Lessmore92
 * Date: 12/12/2019
 * Time: 11:49 PM
 */

namespace Lessmore92\ApiConsumer\Foundation;

use Lessmore92\ApiConsumer\Contracts\ModelInterface as ModelContract;
use ReflectionClass;

abstract class Model implements ModelContract
{
    protected $lock       = true;
    private   $attributes = [];
    private   $caller_class;
    private $tags      = ['property'];
    private $notations = [];

    /**
     * ModelInterface constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->caller_class = get_called_class();
        $this->fire();
        foreach ((array)$attributes as $key => $attribute)
        {
            $this->{$key} = $attribute;
        }
    }

    protected function fire()
    {
        $this->parse_properties();
    }

    private function parse_properties($doc = null)
    {
        if (empty($this->notations))
        {
            if (is_null($doc))
            {
                $doc        = '';
                $reflection = new ReflectionClass($this);
                do
                {
                    if (is_subclass_of($reflection->name, ModelContract::class))
                    {
                        $doc .= $reflection->getDocComment();
                    }

                } while ($reflection = $reflection->getParentClass());
            }

            $notations = $this->extract_notations($doc);
            $notations = $this->join_multiline_notations($notations);
            foreach ($notations as $_notation)
            {
                if (!in_array($_notation['tag'], $this->tags))
                {
                    continue;
                }
                $notation                           = $this->parse_tag($_notation['value']);
                $this->notations[$notation['name']] = ['type' => $notation['type'], 'is_array' => $notation['is_array']];
            }
        }
    }

    /**
     * Extract notation from doc comment
     *
     * @param string $doc
     * @return array
     */
    private function extract_notations($doc)
    {
        $matches     = null;
        $tag         = '\s*@(?<tag>\S+)(?:\h+(?<value>\S.*?)|\h*)';
        $tagContinue = '(?:\040){2}(?<multiline_value>\S.*?)';
        $regex       = '/^\s*(?:(?:\/\*)?\*)?(?:' . $tag . '|' . $tagContinue . ')(?:\*\*\/)?\r?$/m';
        return preg_match_all($regex, $doc, $matches, PREG_SET_ORDER) ? $matches : [];
    }

    /**
     * Join multiline notations
     *
     * @param array $rawNotations
     * @return array
     */
    private function join_multiline_notations($rawNotations)
    {
        $result        = [];
        $tagsNotations = $this->filter_tags_notations($rawNotations);
        foreach ($tagsNotations as $item)
        {
            if (!empty($item['tag']))
            {
                $result[] = $item;
            }
            else
            {
                $lastIdx                   = count($result) - 1;
                $result[$lastIdx]['value'] = trim($result[$lastIdx]['value'])
                                             . ' ' . trim($item['multiline_value']);
            }
        }
        return $result;
    }

    /**
     * Remove everything that goes before tags
     *
     * @param array $rawNotations
     * @return array
     */
    private function filter_tags_notations($rawNotations)
    {
        $count = count($rawNotations);
        for ($i = 0; $i < $count; $i++)
        {
            if (!empty($rawNotations[$i]['tag']))
            {
                return array_slice($rawNotations, $i);
            }
        }
        return [];
    }

    ////////////////////DcoParser

    private function parse_tag($tag)
    {
        $is_array = false;
        list($type, $name) = explode(' ', $tag);

        if (stripos($type, '[]') !== false)
        {
            $type     = str_ireplace('[]', '', $type);
            $is_array = true;
        }
        $out['type']     = $type;
        $out['name']     = $name;
        $out['is_array'] = $is_array;

        return $out;
    }

    public function &__get($key)
    {
        $value = null;
        if (isset($this->attributes[$key]))
        {
            $value = $this->attributes[$key];
        }

        $value = $this->executeCustomGet($key, $value);
        return $value;
    }

    public function __set($key, $value)
    {
        if (!$this->should_be_added_to_model($key))
        {
            return;
        }
        $value                  = $this->executeCustomSet($key, $value);
        $this->attributes[$key] = $this->cast_value($key, $value);
    }

    private function should_be_added_to_model($key)
    {
        if ($this->lock && !isset($this->notations[$key]))
        {
            return false;
        }
        return true;
    }

    protected function executeCustomSet($key, $value)
    {
        $_value = $value;
        if ($this->hasCustomSet($key))
        {
            $_value = $this->{"set" . $key . "attribute"}($value);
        }
        return $_value;
    }

    protected function hasCustomSet($key)
    {
        return method_exists($this, "set" . $key . "attribute");
    }

    private function cast_value($key, $value)
    {
        $_value = $value;
        $cache  = ModelObjectCache::instance()
                                  ->getCache($this->caller_class, 'cast_' . $key)
        ;

        if ($cache)
        {
            $type = $cache;
        }
        else
        {
            $type = $this->cast_type($key);
            ModelObjectCache::instance()
                            ->setCache($this->caller_class, 'cast_' . $key, $type)
            ;
        }


        if ($type['type'] && $type['is_native'])
        {
            settype($_value, $type['type']);
        }
        else if ($type['type'] && $type['is_array'])
        {
            $_value = [];
            foreach ((array)$value as $item)
            {
                $_value[] = new $type['type']($item);
            }
        }
        elseif ($type['type'])
        {
            $_value = new $type['type']($value);
        }

        return $_value;
    }

    private function cast_type($key)
    {
        $type = [
            'type'      => null,
            'is_array'  => false,
            'is_native' => false,
        ];
        if (isset($this->notations[$key]))
        {
            set_error_handler([$this, 'ignore_warnings'], E_WARNING);
            if (class_exists($this->notations[$key]['type']))
            {
                if ($this->notations[$key]['is_array'])
                {
                    $type['type']     = $this->notations[$key]['type'];
                    $type['is_array'] = true;
                }
                else
                {
                    $type['type'] = $this->notations[$key]['type'];
                }
            }
            else
            {
                $type['type']      = $this->notations[$key]['type'];
                $type['is_native'] = true;

            }
            restore_error_handler();
        }
        return $type;
    }

    protected function executeCustomGet($key, $value)
    {
        $_value = $value;
        if (method_exists($this, "get" . $key . "attribute"))
        {
            $_value = $this->{"get" . $key . "attribute"}($value);
        }
        return $_value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $out = [];
        foreach ($this->attributes as $key => $attribute)
        {
            if (is_array($attribute) && isset($attribute[0]) && $attribute[0] instanceof ModelContract)
            {
                foreach ($attribute as $_key => $item)
                {
                    $out[$key][$_key] = $item->toArray();
                }
            }
            else if ($attribute instanceof ModelContract)
            {
                $out[$key] = $attribute->toArray();
            }
            else
            {
                //force run __get
                $out[$key] = $this->{$key};
            }
        }
        return $out;
    }

    public function ignore_warnings($error_no, $error_str)
    {

    }
}

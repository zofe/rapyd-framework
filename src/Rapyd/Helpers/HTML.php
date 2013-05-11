<?php

namespace Rapyd\Helpers;

class HTML
{

    /**
     * array to string for attributes
     *
     * @param   array   $attributes  Array of tags
     * @return  string
     */
    public static function attributes(Array $attributes)
    {
        $attr = '';

        if (count($attributes) ) {
            foreach ($attributes as $attribute => $value) {
                if (is_int($attribute)) {
                    $attribute = $value;
                }
                $attr .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        return $attr;
    }

    /**
     * Creates a HTML tag.
     *
     * @param   string  $name        Tag name
     * @param   array   $attributes  (optional) Tag attributes
     * @param   string  $content     (optional) Tag content
     * @return  string
     */
    public static function tag($name, array $attributes = array(), $content = null)
    {
        return '<' . $name . static::attributes($attributes) . (($content === null) ? ' />' : '>' . $content . '</' . $name . '>');
    }

    /**
     * Open a HTML tag
     * 
     * @param   string    $name
     * @param   array     $attributes
     * @return  string
     */
    public static function open($name, array $attributes = array())
    {
        return '<' . $name . static::attributes($attributes) . ' />';
    }

    /**
     * Close a HTML tag
     * 
     * @param   string    $name
     * @param   array     $attributes
     * @return  string
     */
    public static function close($name)
    {
        return '</' . $name . '>';
    }

    /**
     * Helper method for building media tags.
     *
     * @param   string     $type        Tag type
     * @param   mixed      $files       File or array of files
     * @param   array      $attributes  (optional) Tag attributes
     */
    protected static function buildMedia($type, $files, $attributes)
    {
        $sources = '';

        foreach ((array) $files as $file) {
            $sources .= HTML::tag('source', array('src' => $file));
        }

        return static::tag($type, $attributes, $sources);
    }

    /**
     * Creates audio tag with support for multiple sources.
     *
     * @param   mixed   $files       File or array of files
     * @param   array   $attributes  (optional) Tag attributes
     */
    public static function audio($files, array $attributes = array())
    {
        return static::buildMedia('audio', $files, $attributes);
    }

    /**
     * Creates video tag with support for multiple sources.
     *
     * @param   mixed   $files       File or array of files
     * @param   array   $attributes  (optional) Tag attributes
     */
    public static function video($files, array $attributes = array())
    {
        return static::buildMedia('video', $files, $attributes);
    }

    /**
     * Helper method for building list tags.
     *
     * @param   string     $type        Tag type
     * @param   mixed      $items       File or array of files
     * @param   array      $attributes  (optional) Tag attributes
     */
    protected static function buildList($type, $items, $attributes)
    {
        $list = '';

        foreach ($items as $item) {
            if (is_array($item)) {
                $list .= static::tag('li', array(), static::buildList($type, $item, array()));
            } else {
                $list .= static::tag('li', array(), $item);
            }
        }

        return static::tag($type, $attributes, $list);
    }

    /**
     * Builds an un-ordered list.
     *
     * @param   array   $items       List items
     * @param   array   $attributes  List attributes
     * @return  string
     */
    public static function ul(array $items, array $attributes = array())
    {
        return static::buildList('ul', $items, $attributes);
    }

    /**
     * Builds am ordered list.
     *
     * @param   array   $items       List items
     * @param   array   $attributes  List attributes
     * @return  string
     */
    public static function ol(array $items, array $attributes = array())
    {
        return static::buildList('ol', $items, $attributes);
    }

}
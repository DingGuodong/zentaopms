<?php
/**
 * The base widget class file of zin of ZenTaoPMS.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

require_once __DIR__ . DS . 'props.class.php';
require_once __DIR__ . DS . 'directive.class.php';
require_once __DIR__ . DS . 'zin.class.php';
require_once __DIR__ . DS . 'context.class.php';
require_once __DIR__ . DS . 'selector.func.php';
require_once __DIR__ . DS . 'dom.class.php';

class wg
{
    /**
     * Define props for the element
     *
     * @var array|string
     */
    protected static $defineProps = null;

    protected static $defaultProps = null;

    protected static $defineBlocks = null;

    protected static $wgToBlockMap = array();

    protected static $definedPropsMap = array();

    private static $gidSeed = 0;

    private static $pageResources = array();

    /**
     * The props of the element
     *
     * @access public
     * @var    props
     */
    public $props;

    public $blocks = array();

    public $parent = null;

    public $gid;

    public $displayed = false;

    protected $matchedPortals = null;

    protected $renderOptions = null;

    public function __construct(/* string|element|object|array|null ...$args */)
    {
        $this->props = new props();

        $this->gid = static::nextGid();
        $this->setDefaultProps(static::getDefaultProps());
        $this->add(func_get_args());
        $this->created();

        zin::renderInGlobal($this);
        static::checkPageResources();

        $this->checkErrors();
    }

    public function __debugInfo()
    {
        return $this->toJsonData();
    }

    public function isDomElement()
    {
        return false;
    }

    /**
     * Check if the element is match any of the selectors
     * @param  string|array|object $selectors
     */
    public function isMatch($selectors)
    {
        $list = parseWgSelectors($selectors);
        foreach($list as $selector)
        {
            if(isset($selector->command)) continue;
            if(!empty($selector->id)    && $this->id() !== $selector->id) continue;
            if(!empty($selector->tag)   && $this->shortType() !== $selector->tag) continue;
            if(!empty($selector->class) && !$this->props->class->has($selector->class)) continue;
            return true;
        }
        return false;
    }

    /**
     * Build dom object
     * @return dom
     */
    public function buildDom()
    {
        $before    = $this->buildBefore();
        $children  = $this->build();
        $after     = $this->buildAfter();
        $options   = $this->renderOptions;
        $selectors = (!empty($options) && isset($options['selector'])) ? $options['selector'] : null;

        return new dom
        (
            $this,
            [$before, $children, $after],
            $selectors,
            (!empty($options) && isset($options['type'])) ? $options['type'] : 'html', // TODO: () may not work in lower php
            (!empty($options) && isset($options['data'])) ? $options['data'] : null,
        );
    }

    /**
     * Render widget to html
     * @return string
     */
    public function render()
    {
        $dom    = $this->buildDom();
        $result = $dom->render();

        return is_string($result) ? $result : json_encode($result);
    }

    public function display(?array $options = array()): wg
    {
        zin::disableGlobalRender();
        $this->renderOptions = $options;

        $dom     = $this->buildDom();
        $result  = $dom->render();
        $context = context::current();
        $css     = $context->getCSS();
        $js      = $context->getJS();

        global $app, $config;
        $zinDebug = null;
        if($config->debug)
        {
            $zinDebug = data('zinDebug');
            $zinDebug['basePath'] = $app->getBasePath();
            if(isset($app->zinErrors)) $zinDebug['errors'] = $app->zinErrors;
        }

        $rawContent = ob_get_contents();
        if(!is_string($rawContent)) $rawContent = '';
        ob_end_clean();

        if(is_object($result))
        {
            if($zinDebug && isset($result['zinDebug'])) $result['zinDebug'] = $zinDebug;
            $result = json_encode($result);
        }
        elseif(is_array($result))
        {
            foreach($result as $name => $item)
            {
                if(!isset($item['type']) || $item['type'] !== 'html') continue;

                $item['data'] = str_replace('/*{{ZIN_PAGE_CSS}}*/',     $css,        $item['data']);
                $item['data'] = str_replace('/*{{ZIN_PAGE_JS}}*/',      $js,         $item['data']);
                $item['data'] = str_replace('<!-- {{RAW_CONTENT}} -->', $rawContent, $item['data']);

                $result[$name]['data'] = $item['data'];
            }
            if($zinDebug && isset($result['zinDebug'])) $result['zinDebug'] = $zinDebug;
            $result = json_encode($result);
        }
        else
        {
            if($zinDebug) $js .= h::createJsVarCode('window.zinDebug', $zinDebug);
            $result = str_replace('/*{{ZIN_PAGE_CSS}}*/', $css, $result);
            $result = str_replace('/*{{ZIN_PAGE_JS}}*/', $js, $result);
            $result = str_replace('<!-- {{RAW_CONTENT}} -->', $rawContent, $result);
        }

        echo $result;

        $this->displayed = true;
        context::destroy();
        return $this;
    }

    protected function created() {}

    protected function buildBefore()
    {
        return $this->block('before');
    }

    protected function buildAfter()
    {
        return $this->block('after');
    }

    protected function build()
    {
        return $this->children();
    }

    public function buildEvents()
    {
        $events = $this->props->events();
        if(empty($events)) return null;

        $id   = $this->id();
        $code = array($this->shortType() === 'html' ? 'const ele = document;' : 'const ele = document.getElementById("' . (empty($id) ? $this->gid : $id) . '");if(!ele)return;const $ele = $(ele); const events = new Set(($ele.attr("data-zin-events") || "").split(" ").filter(Boolean));');
        foreach($events as $event => $bindingList)
        {
            $code[]   = "\$ele.on('$event.on.zin', function(e){";
            foreach($bindingList as $binding)
            {
                if(is_string($binding)) $binding = (object)array('handler' => $binding);
                $selector = isset($binding->selector) ? $binding->selector : null;
                $handler  = isset($binding->handler) ? trim($binding->handler) : '';
                $stop     = isset($binding->stop) ? $binding->stop : null;
                $prevent  = isset($binding->prevent) ? $binding->prevent : null;
                $self     = isset($binding->self) ? $binding->self : null;

                $code[]   = '(function(){';
                if($selector) $code[] = "const target = e.target.closest('$selector');if(!target) return;";
                else          $code[] = "const target = ele;";
                if($self)     $code[] = "if(ele !== e.target) return;";
                if($stop)     $code[] = "e.stopPropagation();";
                if($prevent)  $code[] = "e.preventDefault();";

                if(preg_match('/^[$A-Z_][0-9A-Z_$\[\]."\']*$/i', $handler)) $code[] = "($handler).call(target,e);";
                else $code[] = $handler;

                $code[] = '})();';
            }
            $code[] = "});events.add('$event');";
        }
        $code[] = '$ele.attr("data-zin-events", Array.from(events).join(" "));';
        return h::createJsScopeCode($code);
    }


    protected function onAddBlock($child, $name)
    {
        return $child;
    }

    protected function onAddChild($child)
    {
        return $child;
    }

    protected function onSetProp($prop, $value)
    {
        if($prop === 'id' && $value === '$GID') $value = $this->gid;
        if($prop[0] === '@')
        {
            $this->setDefaultProps(['id' => '$GID']);
            context::current()->addWgWithEvents($this);
        }
        $this->props->set($prop, $value);
    }

    protected function onGetProp($prop, $defaultValue)
    {
        return $this->props->get($prop, $defaultValue);
    }

    public function add($item, $blockName = 'children')
    {
        if($item === null || is_bool($item)) return $this;

        if(is_array($item))
        {
            foreach($item as $child) $this->add($child, $blockName);
            return $this;
        }

        zin::disableGlobalRender();

        if($item instanceof wg)    $this->addToBlock($blockName, $item);
        elseif(is_string($item))   $this->addToBlock($blockName, htmlentities($item));
        elseif(isDirective($item)) $this->directive($item, $blockName);
        else                       $this->addToBlock($blockName, htmlentities(strval($item)));

        zin::enableGlobalRender();

        return $this;
    }

    public function addToBlock($name, $child = null)
    {
        if(is_array($name))
        {
            foreach($name as $blockName => $blockChildren)
            {
                $this->addToBlock($blockName, $blockChildren);
            }
            return;
        }
        if(is_array($child))
        {
            foreach($child as $blockChild)
            {
                $this->addToBlock($name, $blockChild);
            }
            return;
        }

        if($child instanceof wg && empty($child->parent)) $child->parent = &$this;

        if($name === 'children' && $child instanceof wg)
        {
            $blockName = static::getBlockNameForWg($child);
            if($blockName !== null) $name = $blockName;
        }

        $result = $name === 'children' ? $this->onAddChild($child) : $this->onAddBlock($child, $name);

        if($result === false) return;
        if($result !== null && $result !== true) $child = $result;

        if(isset($this->blocks[$name])) $this->blocks[$name][] = $child;
        else $this->blocks[$name] = array($child);
    }

    public function children()
    {
        return $this->block('children');
    }

    public function block($name)
    {
        return isset($this->blocks[$name]) ? $this->blocks[$name] : array();
    }

    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }

    /**
     * Apply directive
     * @param object $directive
     */
    public function directive(&$directive, $blockName)
    {
        $data = $directive->data;
        $type = $directive->type;
        $directive->parent = &$this;

        if($type === 'prop')
        {
            $this->setProp($data);
            return;
        }
        if($type === 'class' || $type === 'style')
        {
            $this->setProp($type, $data);
            return;
        }
        if($type === 'cssVar')
        {
            $this->setProp('--', $data);
            return;
        }
        if($type === 'html')
        {
            $this->addToBlock($blockName, $directive);
            return;
        }
        if($type === 'text')
        {
            $this->addToBlock($blockName, htmlspecialchars($data));
            return;
        }
        if($type === 'block')
        {
            foreach($data as $blockName => $blockChildren)
            {
                $this->add($blockChildren, $blockName);
            }
            return;
        }
    }

    public function prop($name, $defaultValue = null)
    {
        if(is_array($name))
        {
            $values = array();
            foreach($name as $index => $propName)
            {
                $values[] = $this->onGetProp($propName, is_array($defaultValue) ? (isset($defaultValue[$propName]) ? $defaultValue[$propName] : $defaultValue[$index]) : $defaultValue);
            }
            return $values;
        }

        return $this->onGetProp($name, $defaultValue);
    }

    /**
     * Set property, an array can be passed to set multiple properties
     *
     * @access public
     * @param array|string   $prop        - Property name or properties list
     * @param mixed          $value       - Property value
     * @return dataset
     */
    public function setProp($prop, $value = null)
    {
        if($prop instanceof props) $prop = $prop->toJsonData();

        if(is_array($prop))
        {
            foreach($prop as $name => $value) $this->setProp($name, $value);
            return $this;
        }

        if(!is_string($prop) || empty($prop)) return $this;

        if($prop[0] === '#')
        {
            $this->add($value, substr($prop, 1));
            return;
        }

        $this->onSetProp($prop, $value);
        return $this;
    }

    public function hasProp()
    {
        $names = func_get_args();
        if(empty($names)) return false;
        foreach ($names as $name) if(!$this->props->has($name)) return false;
        return true;
    }

    public function setDefaultProps($props)
    {
        if(!is_array($props) || empty($props)) return;

        foreach($props as $name => $value)
        {
            if($this->props->has($name)) continue;
            $this->setProp($name, $value);
        }
    }

    public function getRestProps()
    {
        return $this->props->skip(array_keys(static::getDefinedProps()));
    }

    public function type()
    {
        return get_called_class();
    }

    public function shortType()
    {
        $type = $this->type();
        $pos = strrpos($type, '\\');
        return $pos === false ? $type : substr($type, $pos + 1);
    }

    public function id()
    {
        return $this->prop('id');
    }

    public function toJsonData()
    {
        $data = array();
        $data['gid'] = $this->gid;
        $data['props'] = $this->props->toJsonData();

        $data['type'] = $this->type();
        if(str_starts_with($data['type'], 'zin\\')) $data['type'] = substr($data['type'], 4);

        $data['blocks'] = array();
        foreach($this->blocks as $key => $value)
        {
            foreach($value as $index => $child)
            {
                if($child instanceof wg || (is_object($child) && method_exists($child, 'toJsonData')))
                {
                    $value[$index] = $child->toJsonData();
                }
                elseif(isDirective($child, 'html'))
                {
                    $value[$index] = $child->data;
                }
            }
            if($key === 'children')
            {
                unset($data['blocks'][$key]);
                $data['children'] = $value;
            }
            else
            {
                $data['blocks'][$key] = $value;
            }
        }

        if(empty($data['blocks'])) unset($data['blocks']);

        if(!empty($this->parent)) $data['parent'] = $this->parent->gid;

        return $data;
    }

    /**
     * Check errors in debug mode.
     *
     * @access protected
     * @return void
     */
    protected function checkErrors()
    {
        global $config;
        if(!isset($config->debug) || !$config->debug) return;

        $definedProps = static::getDefinedProps();
        foreach($definedProps as $name => $definition)
        {
            if($this->hasProp($name)) continue;
            if(isset($definition['default']) && $definition['default'] !== null) continue;
            if(isset($definition['optional']) && $definition['optional']) continue;

            trigger_error("[ZIN] The property \"$name: {$definition['type']}\" of widget \"{$this->type()}#$this->gid\" is required.", E_USER_ERROR);
        }

        $wgErrors = $this->onCheckErrors();
        if(empty($wgErrors)) return;

        foreach($wgErrors as $error)
        {
            if(is_array($error)) trigger_error("[ZIN] $error[0]", count($error) > 1 ? $error[1] : E_USER_WARNING);
            else trigger_error("[ZIN] $error", E_USER_ERROR);
        }
    }

    /**
     * The lifecycle method for checking errors in debug mode.
     *
     * @access protected
     * @return array|null
     */
    protected function onCheckErrors(): array|null
    {
        return null;
    }

    public static function getPageCSS(): string|false
    {
        return false; // No css
    }

    public static function getPageJS(): string|false
    {
        return false; // No js
    }

    protected static function checkPageResources()
    {
        $name = get_called_class();
        if(isset(static::$pageResources[$name])) return;

        static::$pageResources[$name] = true;

        $pageCSS = static::getPageCSS();
        $pageJS  = static::getPageJS();

        if(!empty($pageCSS)) context::css($pageCSS);
        if(!empty($pageJS))  context::js($pageJS);
    }

    public static function wgBlockMap()
    {
        $wgName = get_called_class();
        if(!isset(wg::$wgToBlockMap[$wgName]))
        {
            $wgBlockMap = array();
            if(isset(static::$defineBlocks))
            {
                foreach(static::$defineBlocks as $blockName => $setting)
                {
                    if(!isset($setting['map'])) continue;
                    $map = $setting['map'];
                    if(is_string($map)) $map = explode(',', $map);
                    foreach($map as $name) $wgBlockMap[$name] = $blockName;
                }
            }
            wg::$wgToBlockMap[$wgName] = $wgBlockMap;
        }
        return wg::$wgToBlockMap[$wgName];
    }

    public static function getBlockNameForWg($wg)
    {
        $wgType = ($wg instanceof wg) ? $wg->type() : $wg;
        $wgBlockMap = static::wgBlockMap();
        if(str_starts_with($wgType, 'zin\\')) $wgType = substr($wgType, 4);
        return isset($wgBlockMap[$wgType]) ? $wgBlockMap[$wgType] : null;
    }

    public static function nextGid()
    {
        return 'zin' . (++static::$gidSeed);
    }

    protected static function getDefinedProps(string|null $wgName = null): array
    {
        if($wgName === null) $wgName = get_called_class();

        if(!isset(wg::$definedPropsMap[$wgName]) && $wgName === get_called_class())
        {
            wg::$definedPropsMap[$wgName] = static::parsePropsDefinition(static::$defineProps);
        }
        return wg::$definedPropsMap[$wgName];
    }

    protected static function getDefaultProps(string|null $wgName = null): array
    {
        $defaultProps = array();
        foreach(static::getDefinedProps($wgName) as $name => $definition)
        {
            if(!isset($definition['default'])) continue;
            $defaultProps[$name] = $definition['default'];
        }
        return $defaultProps;
    }

    /**
     * Parse props definition
     * @param $definition
     * @example
     *
     * $definition = 'name,desc:string,title?:string|element,icon?:string="star"'
     * $definition = array('name', 'desc:string', 'title?:string|element', 'icon?:string="star"');
     * $definition = array('name' => 'mixed', 'desc' => 'string', 'title' => array('type' => 'string|element', 'optional' => true), 'icon' => array('type' => 'string', 'default' => 'star', 'optional' => true))))
     */
    private static function parsePropsDefinition($definition)
    {
        $parentClass = get_parent_class(get_called_class());
        $props = $parentClass ? call_user_func("$parentClass::getDefinedProps") : array();

        if((!is_array($definition) && !is_string($definition)) || ($parentClass && $definition === $parentClass::$defineProps))
        {
            if(static::$defaultProps && static::$defaultProps !== $parentClass::$defaultProps)
            {
                foreach($props as $name => $value)
                {
                    if(is_array(static::$defaultProps) && isset(static::$defaultProps[$name]))
                    {
                        $value['default'] = static::$defaultProps[$name];
                        $props[$name]     = $value;
                    }
                }
            }
            return $props;
        }

        if(is_string($definition)) $definition = explode(',', $definition);

        foreach($definition as $name => $value)
        {
            $optional = false;
            $type     = 'mixed';
            $default  = (isset($props[$name]) && isset($props[$name]['default'])) ? $props[$name]['default'] : null;

            if(is_int($name) && is_string($value))
            {
                $value = trim($value);
                if(!str_contains($value, ':'))
                {
                    $name  = $value;
                    $value = '';
                }
                else
                {
                    list($name, $value) = explode(':', $value, 2);
                }
                $name = trim($name);
                if($name[strlen($name) - 1] === '?')
                {
                    $name     = substr($name, 0, strlen($name) - 1);
                    $optional = true;
                }
            }

            if(is_array($value))
            {
                $type     = isset($value['type'])    ? $value['type']    : $type;
                $default  = isset($value['default']) ? $value['default'] : $default;
                $optional = isset($value['optional'])? $value['optional']: $optional;
            }
            else if(is_string($value))
            {
                if(!str_contains($value, '='))
                {
                    $type    = $value;
                    $default = null;
                }
                else
                {
                    list($type, $default) = explode('=', $value, 2);
                }
                $type = trim($type);

                if(is_string($default)) $default = json_decode(trim($default));
            }

            $props[$name] = array('type' => empty($type) ? 'mixed' : $type, 'default' => $default, 'optional' => $default !== null || $optional);
        }

        if(static::$defaultProps && (!$parentClass || static::$defaultProps !== $parentClass::$defaultProps))
        {
            foreach(static::$defaultProps as $name => $value)
            {
                if(!isset($props[$name])) continue;
                $props[$name]['default'] = $value;
            }
        }
        return $props;
    }
}

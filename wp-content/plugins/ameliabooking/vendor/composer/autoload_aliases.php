<?php

// Functions and constants

namespace {
    if(!function_exists('\\getallheaders')){
        function getallheaders(...$args) {
            return \ameliavendor_getallheaders(...func_get_args());
        }
    }
    if(!function_exists('\\trigger_deprecation')){
        function trigger_deprecation(...$args) {
            return \ameliavendor_trigger_deprecation(...func_get_args());
        }
    }

}
namespace AmeliaVendor\GuzzleHttp {
}
namespace Sabre\Uri {
    if(!function_exists('\\Sabre\\Uri\\resolve')){
        function resolve(...$args) {
            return \AmeliaVendor\Sabre\Uri\resolve(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Uri\\normalize')){
        function normalize(...$args) {
            return \AmeliaVendor\Sabre\Uri\normalize(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Uri\\parse')){
        function parse(...$args) {
            return \AmeliaVendor\Sabre\Uri\parse(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Uri\\build')){
        function build(...$args) {
            return \AmeliaVendor\Sabre\Uri\build(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Uri\\split')){
        function split(...$args) {
            return \AmeliaVendor\Sabre\Uri\split(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Uri\\_parse_fallback')){
        function _parse_fallback(...$args) {
            return \AmeliaVendor\Sabre\Uri\_parse_fallback(...func_get_args());
        }
    }
}
namespace Sabre\VObject {
    if(!function_exists('\\Sabre\\VObject\\writeStats')){
        function writeStats(...$args) {
            return \AmeliaVendor\Sabre\VObject\writeStats(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\VObject\\write')){
        function write(...$args) {
            return \AmeliaVendor\Sabre\VObject\write(...func_get_args());
        }
    }
}
namespace Sabre\Xml\Deserializer {
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\keyValue')){
        function keyValue(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\keyValue(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\enum')){
        function enum(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\enum(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\valueObject')){
        function valueObject(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\valueObject(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\repeatingElements')){
        function repeatingElements(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\repeatingElements(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\mixedContent')){
        function mixedContent(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\mixedContent(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Deserializer\\functionCaller')){
        function functionCaller(...$args) {
            return \AmeliaVendor\Sabre\Xml\Deserializer\functionCaller(...func_get_args());
        }
    }
}
namespace Sabre\Xml\Serializer {
    if(!function_exists('\\Sabre\\Xml\\Serializer\\enum')){
        function enum(...$args) {
            return \AmeliaVendor\Sabre\Xml\Serializer\enum(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Serializer\\valueObject')){
        function valueObject(...$args) {
            return \AmeliaVendor\Sabre\Xml\Serializer\valueObject(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Serializer\\repeatingElements')){
        function repeatingElements(...$args) {
            return \AmeliaVendor\Sabre\Xml\Serializer\repeatingElements(...func_get_args());
        }
    }
    if(!function_exists('\\Sabre\\Xml\\Serializer\\standardSerializer')){
        function standardSerializer(...$args) {
            return \AmeliaVendor\Sabre\Xml\Serializer\standardSerializer(...func_get_args());
        }
    }
}


namespace AmeliaVendor {

    use BrianHenryIE\Strauss\Types\AutoloadAliasInterface;

    /**
     * @see AutoloadAliasInterface
     *
     * @phpstan-type ClassAliasArray array{'type':'class',isabstract:bool,classname:string,namespace?:string,extends:string,implements:array<string>}
     * @phpstan-type InterfaceAliasArray array{'type':'interface',interfacename:string,namespace?:string,extends:array<string>}
     * @phpstan-type TraitAliasArray array{'type':'trait',traitname:string,namespace?:string,use:array<string>}
     * @phpstan-type AutoloadAliasArray array<string,ClassAliasArray|InterfaceAliasArray|TraitAliasArray>
     */
    class AliasAutoloader
    {
        private string $includeFilePath;

        /**
         * @var AutoloadAliasArray
         */
        private array $autoloadAliases = array (
  'Dompdf\\Cpdf' => 
  array (
    'type' => 'class',
    'classname' => 'Cpdf',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\Cpdf',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Adapter\\CPDF' => 
  array (
    'type' => 'class',
    'classname' => 'CPDF',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Adapter',
    'extends' => 'AmeliaVendor\\Dompdf\\Adapter\\CPDF',
    'implements' => 
    array (
      0 => 'Dompdf\\Canvas',
    ),
  ),
  'Dompdf\\Adapter\\GD' => 
  array (
    'type' => 'class',
    'classname' => 'GD',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Adapter',
    'extends' => 'AmeliaVendor\\Dompdf\\Adapter\\GD',
    'implements' => 
    array (
      0 => 'Dompdf\\Canvas',
    ),
  ),
  'Dompdf\\Adapter\\PDFLib' => 
  array (
    'type' => 'class',
    'classname' => 'PDFLib',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Adapter',
    'extends' => 'AmeliaVendor\\Dompdf\\Adapter\\PDFLib',
    'implements' => 
    array (
      0 => 'Dompdf\\Canvas',
    ),
  ),
  'Dompdf\\CanvasFactory' => 
  array (
    'type' => 'class',
    'classname' => 'CanvasFactory',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\CanvasFactory',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Cellmap' => 
  array (
    'type' => 'class',
    'classname' => 'Cellmap',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\Cellmap',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\AttributeTranslator' => 
  array (
    'type' => 'class',
    'classname' => 'AttributeTranslator',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\AttributeTranslator',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Color' => 
  array (
    'type' => 'class',
    'classname' => 'Color',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Color',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\Attr' => 
  array (
    'type' => 'class',
    'classname' => 'Attr',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\Attr',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\CloseQuote' => 
  array (
    'type' => 'class',
    'classname' => 'CloseQuote',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\CloseQuote',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\ContentPart' => 
  array (
    'type' => 'class',
    'classname' => 'ContentPart',
    'isabstract' => true,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\ContentPart',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\Counter' => 
  array (
    'type' => 'class',
    'classname' => 'Counter',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\Counter',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\Counters' => 
  array (
    'type' => 'class',
    'classname' => 'Counters',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\Counters',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\NoCloseQuote' => 
  array (
    'type' => 'class',
    'classname' => 'NoCloseQuote',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\NoCloseQuote',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\NoOpenQuote' => 
  array (
    'type' => 'class',
    'classname' => 'NoOpenQuote',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\NoOpenQuote',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\OpenQuote' => 
  array (
    'type' => 'class',
    'classname' => 'OpenQuote',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\OpenQuote',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\StringPart' => 
  array (
    'type' => 'class',
    'classname' => 'StringPart',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\StringPart',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Content\\Url' => 
  array (
    'type' => 'class',
    'classname' => 'Url',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css\\Content',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Content\\Url',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Style',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Css\\Stylesheet' => 
  array (
    'type' => 'class',
    'classname' => 'Stylesheet',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Css',
    'extends' => 'AmeliaVendor\\Dompdf\\Css\\Stylesheet',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Dompdf' => 
  array (
    'type' => 'class',
    'classname' => 'Dompdf',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\Dompdf',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Exception\\ImageException' => 
  array (
    'type' => 'class',
    'classname' => 'ImageException',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Exception',
    'extends' => 'AmeliaVendor\\Dompdf\\Exception\\ImageException',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FontMetrics' => 
  array (
    'type' => 'class',
    'classname' => 'FontMetrics',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\FontMetrics',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Frame\\Factory' => 
  array (
    'type' => 'class',
    'classname' => 'Factory',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Frame',
    'extends' => 'AmeliaVendor\\Dompdf\\Frame\\Factory',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Frame\\FrameListIterator' => 
  array (
    'type' => 'class',
    'classname' => 'FrameListIterator',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Frame',
    'extends' => 'AmeliaVendor\\Dompdf\\Frame\\FrameListIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'Dompdf\\Frame\\FrameTree' => 
  array (
    'type' => 'class',
    'classname' => 'FrameTree',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Frame',
    'extends' => 'AmeliaVendor\\Dompdf\\Frame\\FrameTree',
    'implements' => 
    array (
      0 => 'IteratorAggregate',
    ),
  ),
  'Dompdf\\Frame\\FrameTreeIterator' => 
  array (
    'type' => 'class',
    'classname' => 'FrameTreeIterator',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Frame',
    'extends' => 'AmeliaVendor\\Dompdf\\Frame\\FrameTreeIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'Dompdf\\FrameDecorator\\AbstractFrameDecorator' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractFrameDecorator',
    'isabstract' => true,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\AbstractFrameDecorator',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Block' => 
  array (
    'type' => 'class',
    'classname' => 'Block',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Block',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Image' => 
  array (
    'type' => 'class',
    'classname' => 'Image',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Image',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Inline' => 
  array (
    'type' => 'class',
    'classname' => 'Inline',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Inline',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\ListBullet' => 
  array (
    'type' => 'class',
    'classname' => 'ListBullet',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\ListBullet',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\ListBulletImage' => 
  array (
    'type' => 'class',
    'classname' => 'ListBulletImage',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\ListBulletImage',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\NullFrameDecorator' => 
  array (
    'type' => 'class',
    'classname' => 'NullFrameDecorator',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\NullFrameDecorator',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Page' => 
  array (
    'type' => 'class',
    'classname' => 'Page',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Page',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Table' => 
  array (
    'type' => 'class',
    'classname' => 'Table',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Table',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\TableCell' => 
  array (
    'type' => 'class',
    'classname' => 'TableCell',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\TableCell',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\TableRow' => 
  array (
    'type' => 'class',
    'classname' => 'TableRow',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\TableRow',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\TableRowGroup' => 
  array (
    'type' => 'class',
    'classname' => 'TableRowGroup',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\TableRowGroup',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameDecorator\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameDecorator',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameDecorator\\Text',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\AbstractFrameReflower' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractFrameReflower',
    'isabstract' => true,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\AbstractFrameReflower',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Block' => 
  array (
    'type' => 'class',
    'classname' => 'Block',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Block',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Image' => 
  array (
    'type' => 'class',
    'classname' => 'Image',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Image',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Inline' => 
  array (
    'type' => 'class',
    'classname' => 'Inline',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Inline',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\ListBullet' => 
  array (
    'type' => 'class',
    'classname' => 'ListBullet',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\ListBullet',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\NullFrameReflower' => 
  array (
    'type' => 'class',
    'classname' => 'NullFrameReflower',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\NullFrameReflower',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Page' => 
  array (
    'type' => 'class',
    'classname' => 'Page',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Page',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Table' => 
  array (
    'type' => 'class',
    'classname' => 'Table',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Table',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\TableCell' => 
  array (
    'type' => 'class',
    'classname' => 'TableCell',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\TableCell',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\TableRow' => 
  array (
    'type' => 'class',
    'classname' => 'TableRow',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\TableRow',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\TableRowGroup' => 
  array (
    'type' => 'class',
    'classname' => 'TableRowGroup',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\TableRowGroup',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\FrameReflower\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'Dompdf\\FrameReflower',
    'extends' => 'AmeliaVendor\\Dompdf\\FrameReflower\\Text',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Image\\Cache' => 
  array (
    'type' => 'class',
    'classname' => 'Cache',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Image',
    'extends' => 'AmeliaVendor\\Dompdf\\Image\\Cache',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\JavascriptEmbedder' => 
  array (
    'type' => 'class',
    'classname' => 'JavascriptEmbedder',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\JavascriptEmbedder',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\LineBox' => 
  array (
    'type' => 'class',
    'classname' => 'LineBox',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\LineBox',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Options' => 
  array (
    'type' => 'class',
    'classname' => 'Options',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\Options',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\PhpEvaluator' => 
  array (
    'type' => 'class',
    'classname' => 'PhpEvaluator',
    'isabstract' => false,
    'namespace' => 'Dompdf',
    'extends' => 'AmeliaVendor\\Dompdf\\PhpEvaluator',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\Absolute' => 
  array (
    'type' => 'class',
    'classname' => 'Absolute',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\Absolute',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\AbstractPositioner' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractPositioner',
    'isabstract' => true,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\AbstractPositioner',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\Block' => 
  array (
    'type' => 'class',
    'classname' => 'Block',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\Block',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\Fixed' => 
  array (
    'type' => 'class',
    'classname' => 'Fixed',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\Fixed',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\Inline' => 
  array (
    'type' => 'class',
    'classname' => 'Inline',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\Inline',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\ListBullet' => 
  array (
    'type' => 'class',
    'classname' => 'ListBullet',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\ListBullet',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\NullPositioner' => 
  array (
    'type' => 'class',
    'classname' => 'NullPositioner',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\NullPositioner',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\TableCell' => 
  array (
    'type' => 'class',
    'classname' => 'TableCell',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\TableCell',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Positioner\\TableRow' => 
  array (
    'type' => 'class',
    'classname' => 'TableRow',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Positioner',
    'extends' => 'AmeliaVendor\\Dompdf\\Positioner\\TableRow',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\AbstractRenderer' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractRenderer',
    'isabstract' => true,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\AbstractRenderer',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\Block' => 
  array (
    'type' => 'class',
    'classname' => 'Block',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\Block',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\Image' => 
  array (
    'type' => 'class',
    'classname' => 'Image',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\Image',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\Inline' => 
  array (
    'type' => 'class',
    'classname' => 'Inline',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\Inline',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\ListBullet' => 
  array (
    'type' => 'class',
    'classname' => 'ListBullet',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\ListBullet',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\TableCell' => 
  array (
    'type' => 'class',
    'classname' => 'TableCell',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\TableCell',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\TableRow' => 
  array (
    'type' => 'class',
    'classname' => 'TableRow',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\TableRow',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\TableRowGroup' => 
  array (
    'type' => 'class',
    'classname' => 'TableRowGroup',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\TableRowGroup',
    'implements' => 
    array (
    ),
  ),
  'Dompdf\\Renderer\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'Dompdf\\Renderer',
    'extends' => 'AmeliaVendor\\Dompdf\\Renderer\\Text',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\AdobeFontMetrics' => 
  array (
    'type' => 'class',
    'classname' => 'AdobeFontMetrics',
    'isabstract' => false,
    'namespace' => 'FontLib',
    'extends' => 'AmeliaVendor\\FontLib\\AdobeFontMetrics',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\BinaryStream' => 
  array (
    'type' => 'class',
    'classname' => 'BinaryStream',
    'isabstract' => false,
    'namespace' => 'FontLib',
    'extends' => 'AmeliaVendor\\FontLib\\BinaryStream',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\EOT\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'FontLib\\EOT',
    'extends' => 'AmeliaVendor\\FontLib\\EOT\\File',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\EOT\\Header' => 
  array (
    'type' => 'class',
    'classname' => 'Header',
    'isabstract' => false,
    'namespace' => 'FontLib\\EOT',
    'extends' => 'AmeliaVendor\\FontLib\\EOT\\Header',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\EncodingMap' => 
  array (
    'type' => 'class',
    'classname' => 'EncodingMap',
    'isabstract' => false,
    'namespace' => 'FontLib',
    'extends' => 'AmeliaVendor\\FontLib\\EncodingMap',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Exception\\FontNotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'FontNotFoundException',
    'isabstract' => false,
    'namespace' => 'FontLib\\Exception',
    'extends' => 'AmeliaVendor\\FontLib\\Exception\\FontNotFoundException',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Font' => 
  array (
    'type' => 'class',
    'classname' => 'Font',
    'isabstract' => false,
    'namespace' => 'FontLib',
    'extends' => 'AmeliaVendor\\FontLib\\Font',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Glyph\\Outline' => 
  array (
    'type' => 'class',
    'classname' => 'Outline',
    'isabstract' => false,
    'namespace' => 'FontLib\\Glyph',
    'extends' => 'AmeliaVendor\\FontLib\\Glyph\\Outline',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Glyph\\OutlineComponent' => 
  array (
    'type' => 'class',
    'classname' => 'OutlineComponent',
    'isabstract' => false,
    'namespace' => 'FontLib\\Glyph',
    'extends' => 'AmeliaVendor\\FontLib\\Glyph\\OutlineComponent',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Glyph\\OutlineComposite' => 
  array (
    'type' => 'class',
    'classname' => 'OutlineComposite',
    'isabstract' => false,
    'namespace' => 'FontLib\\Glyph',
    'extends' => 'AmeliaVendor\\FontLib\\Glyph\\OutlineComposite',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Glyph\\OutlineSimple' => 
  array (
    'type' => 'class',
    'classname' => 'OutlineSimple',
    'isabstract' => false,
    'namespace' => 'FontLib\\Glyph',
    'extends' => 'AmeliaVendor\\FontLib\\Glyph\\OutlineSimple',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Header' => 
  array (
    'type' => 'class',
    'classname' => 'Header',
    'isabstract' => true,
    'namespace' => 'FontLib',
    'extends' => 'AmeliaVendor\\FontLib\\Header',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\OpenType\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'FontLib\\OpenType',
    'extends' => 'AmeliaVendor\\FontLib\\OpenType\\File',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\OpenType\\TableDirectoryEntry' => 
  array (
    'type' => 'class',
    'classname' => 'TableDirectoryEntry',
    'isabstract' => false,
    'namespace' => 'FontLib\\OpenType',
    'extends' => 'AmeliaVendor\\FontLib\\OpenType\\TableDirectoryEntry',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\DirectoryEntry' => 
  array (
    'type' => 'class',
    'classname' => 'DirectoryEntry',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\DirectoryEntry',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Table' => 
  array (
    'type' => 'class',
    'classname' => 'Table',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Table',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\cmap' => 
  array (
    'type' => 'class',
    'classname' => 'cmap',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\cmap',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\cvt' => 
  array (
    'type' => 'class',
    'classname' => 'cvt',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\cvt',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\fpgm' => 
  array (
    'type' => 'class',
    'classname' => 'fpgm',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\fpgm',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\glyf' => 
  array (
    'type' => 'class',
    'classname' => 'glyf',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\glyf',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\head' => 
  array (
    'type' => 'class',
    'classname' => 'head',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\head',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\hhea' => 
  array (
    'type' => 'class',
    'classname' => 'hhea',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\hhea',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\hmtx' => 
  array (
    'type' => 'class',
    'classname' => 'hmtx',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\hmtx',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\kern' => 
  array (
    'type' => 'class',
    'classname' => 'kern',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\kern',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\loca' => 
  array (
    'type' => 'class',
    'classname' => 'loca',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\loca',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\maxp' => 
  array (
    'type' => 'class',
    'classname' => 'maxp',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\maxp',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\name' => 
  array (
    'type' => 'class',
    'classname' => 'name',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\name',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\nameRecord' => 
  array (
    'type' => 'class',
    'classname' => 'nameRecord',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\nameRecord',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\os2' => 
  array (
    'type' => 'class',
    'classname' => 'os2',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\os2',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\post' => 
  array (
    'type' => 'class',
    'classname' => 'post',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\post',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\Table\\Type\\prep' => 
  array (
    'type' => 'class',
    'classname' => 'prep',
    'isabstract' => false,
    'namespace' => 'FontLib\\Table\\Type',
    'extends' => 'AmeliaVendor\\FontLib\\Table\\Type\\prep',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\TrueType\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'FontLib\\TrueType',
    'extends' => 'AmeliaVendor\\FontLib\\TrueType\\Collection',
    'implements' => 
    array (
      0 => 'Iterator',
      1 => 'Countable',
    ),
  ),
  'FontLib\\TrueType\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'FontLib\\TrueType',
    'extends' => 'AmeliaVendor\\FontLib\\TrueType\\File',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\TrueType\\Header' => 
  array (
    'type' => 'class',
    'classname' => 'Header',
    'isabstract' => false,
    'namespace' => 'FontLib\\TrueType',
    'extends' => 'AmeliaVendor\\FontLib\\TrueType\\Header',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\TrueType\\TableDirectoryEntry' => 
  array (
    'type' => 'class',
    'classname' => 'TableDirectoryEntry',
    'isabstract' => false,
    'namespace' => 'FontLib\\TrueType',
    'extends' => 'AmeliaVendor\\FontLib\\TrueType\\TableDirectoryEntry',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\WOFF\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'FontLib\\WOFF',
    'extends' => 'AmeliaVendor\\FontLib\\WOFF\\File',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\WOFF\\Header' => 
  array (
    'type' => 'class',
    'classname' => 'Header',
    'isabstract' => false,
    'namespace' => 'FontLib\\WOFF',
    'extends' => 'AmeliaVendor\\FontLib\\WOFF\\Header',
    'implements' => 
    array (
    ),
  ),
  'FontLib\\WOFF\\TableDirectoryEntry' => 
  array (
    'type' => 'class',
    'classname' => 'TableDirectoryEntry',
    'isabstract' => false,
    'namespace' => 'FontLib\\WOFF',
    'extends' => 'AmeliaVendor\\FontLib\\WOFF\\TableDirectoryEntry',
    'implements' => 
    array (
    ),
  ),
  'Svg\\CssLength' => 
  array (
    'type' => 'class',
    'classname' => 'CssLength',
    'isabstract' => false,
    'namespace' => 'Svg',
    'extends' => 'AmeliaVendor\\Svg\\CssLength',
    'implements' => 
    array (
    ),
  ),
  'Svg\\DefaultStyle' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultStyle',
    'isabstract' => false,
    'namespace' => 'Svg',
    'extends' => 'AmeliaVendor\\Svg\\DefaultStyle',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Document' => 
  array (
    'type' => 'class',
    'classname' => 'Document',
    'isabstract' => false,
    'namespace' => 'Svg',
    'extends' => 'AmeliaVendor\\Svg\\Document',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Gradient\\Stop' => 
  array (
    'type' => 'class',
    'classname' => 'Stop',
    'isabstract' => false,
    'namespace' => 'Svg\\Gradient',
    'extends' => 'AmeliaVendor\\Svg\\Gradient\\Stop',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'Svg',
    'extends' => 'AmeliaVendor\\Svg\\Style',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Surface\\CPdf' => 
  array (
    'type' => 'class',
    'classname' => 'CPdf',
    'isabstract' => false,
    'namespace' => 'Svg\\Surface',
    'extends' => 'AmeliaVendor\\Svg\\Surface\\CPdf',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Surface\\SurfaceCpdf' => 
  array (
    'type' => 'class',
    'classname' => 'SurfaceCpdf',
    'isabstract' => false,
    'namespace' => 'Svg\\Surface',
    'extends' => 'AmeliaVendor\\Svg\\Surface\\SurfaceCpdf',
    'implements' => 
    array (
      0 => 'Svg\\Surface\\SurfaceInterface',
    ),
  ),
  'Svg\\Surface\\SurfacePDFLib' => 
  array (
    'type' => 'class',
    'classname' => 'SurfacePDFLib',
    'isabstract' => false,
    'namespace' => 'Svg\\Surface',
    'extends' => 'AmeliaVendor\\Svg\\Surface\\SurfacePDFLib',
    'implements' => 
    array (
      0 => 'Svg\\Surface\\SurfaceInterface',
    ),
  ),
  'Svg\\Tag\\AbstractTag' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractTag',
    'isabstract' => true,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\AbstractTag',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Anchor' => 
  array (
    'type' => 'class',
    'classname' => 'Anchor',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Anchor',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Circle' => 
  array (
    'type' => 'class',
    'classname' => 'Circle',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Circle',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\ClipPath' => 
  array (
    'type' => 'class',
    'classname' => 'ClipPath',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\ClipPath',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Ellipse' => 
  array (
    'type' => 'class',
    'classname' => 'Ellipse',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Ellipse',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Group' => 
  array (
    'type' => 'class',
    'classname' => 'Group',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Group',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Image' => 
  array (
    'type' => 'class',
    'classname' => 'Image',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Image',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Line' => 
  array (
    'type' => 'class',
    'classname' => 'Line',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Line',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\LinearGradient' => 
  array (
    'type' => 'class',
    'classname' => 'LinearGradient',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\LinearGradient',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Path' => 
  array (
    'type' => 'class',
    'classname' => 'Path',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Path',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Polygon' => 
  array (
    'type' => 'class',
    'classname' => 'Polygon',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Polygon',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Polyline' => 
  array (
    'type' => 'class',
    'classname' => 'Polyline',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Polyline',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\RadialGradient' => 
  array (
    'type' => 'class',
    'classname' => 'RadialGradient',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\RadialGradient',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Rect' => 
  array (
    'type' => 'class',
    'classname' => 'Rect',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Rect',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Shape' => 
  array (
    'type' => 'class',
    'classname' => 'Shape',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Shape',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Stop' => 
  array (
    'type' => 'class',
    'classname' => 'Stop',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Stop',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\StyleTag' => 
  array (
    'type' => 'class',
    'classname' => 'StyleTag',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\StyleTag',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Symbol' => 
  array (
    'type' => 'class',
    'classname' => 'Symbol',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Symbol',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\Text',
    'implements' => 
    array (
    ),
  ),
  'Svg\\Tag\\UseTag' => 
  array (
    'type' => 'class',
    'classname' => 'UseTag',
    'isabstract' => false,
    'namespace' => 'Svg\\Tag',
    'extends' => 'AmeliaVendor\\Svg\\Tag\\UseTag',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\BeforeValidException' => 
  array (
    'type' => 'class',
    'classname' => 'BeforeValidException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\BeforeValidException',
    'implements' => 
    array (
      0 => 'Firebase\\JWT\\JWTExceptionWithPayloadInterface',
    ),
  ),
  'Firebase\\JWT\\CachedKeySet' => 
  array (
    'type' => 'class',
    'classname' => 'CachedKeySet',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\CachedKeySet',
    'implements' => 
    array (
      0 => 'ArrayAccess',
    ),
  ),
  'Firebase\\JWT\\ExpiredException' => 
  array (
    'type' => 'class',
    'classname' => 'ExpiredException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\ExpiredException',
    'implements' => 
    array (
      0 => 'Firebase\\JWT\\JWTExceptionWithPayloadInterface',
    ),
  ),
  'Firebase\\JWT\\JWK' => 
  array (
    'type' => 'class',
    'classname' => 'JWK',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\JWK',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\JWT' => 
  array (
    'type' => 'class',
    'classname' => 'JWT',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\JWT',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\Key' => 
  array (
    'type' => 'class',
    'classname' => 'Key',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\Key',
    'implements' => 
    array (
    ),
  ),
  'Firebase\\JWT\\SignatureInvalidException' => 
  array (
    'type' => 'class',
    'classname' => 'SignatureInvalidException',
    'isabstract' => false,
    'namespace' => 'Firebase\\JWT',
    'extends' => 'AmeliaVendor\\Firebase\\JWT\\SignatureInvalidException',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Acl' => 
  array (
    'type' => 'class',
    'classname' => 'Acl',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Acl',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\AclRule' => 
  array (
    'type' => 'class',
    'classname' => 'AclRule',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\AclRule',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\AclRuleScope' => 
  array (
    'type' => 'class',
    'classname' => 'AclRuleScope',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\AclRuleScope',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Calendar' => 
  array (
    'type' => 'class',
    'classname' => 'Calendar',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Calendar',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\CalendarList' => 
  array (
    'type' => 'class',
    'classname' => 'CalendarList',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\CalendarList',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\CalendarListEntry' => 
  array (
    'type' => 'class',
    'classname' => 'CalendarListEntry',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\CalendarListEntry',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\CalendarListEntryNotificationSettings' => 
  array (
    'type' => 'class',
    'classname' => 'CalendarListEntryNotificationSettings',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\CalendarListEntryNotificationSettings',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\CalendarNotification' => 
  array (
    'type' => 'class',
    'classname' => 'CalendarNotification',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\CalendarNotification',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Channel' => 
  array (
    'type' => 'class',
    'classname' => 'Channel',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Channel',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ColorDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'ColorDefinition',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ColorDefinition',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Colors' => 
  array (
    'type' => 'class',
    'classname' => 'Colors',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Colors',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceData' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceData',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceData',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceParameters' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceParameters',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceParameters',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceParametersAddOnParameters' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceParametersAddOnParameters',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceParametersAddOnParameters',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceProperties' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceProperties',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceProperties',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceRequestStatus' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceRequestStatus',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceRequestStatus',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceSolution' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceSolution',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceSolution',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\ConferenceSolutionKey' => 
  array (
    'type' => 'class',
    'classname' => 'ConferenceSolutionKey',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\ConferenceSolutionKey',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\CreateConferenceRequest' => 
  array (
    'type' => 'class',
    'classname' => 'CreateConferenceRequest',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\CreateConferenceRequest',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EntryPoint' => 
  array (
    'type' => 'class',
    'classname' => 'EntryPoint',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EntryPoint',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Error' => 
  array (
    'type' => 'class',
    'classname' => 'Error',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Error',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Event' => 
  array (
    'type' => 'class',
    'classname' => 'Event',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Event',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventAttachment' => 
  array (
    'type' => 'class',
    'classname' => 'EventAttachment',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventAttachment',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventAttendee' => 
  array (
    'type' => 'class',
    'classname' => 'EventAttendee',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventAttendee',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventCreator' => 
  array (
    'type' => 'class',
    'classname' => 'EventCreator',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventCreator',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventDateTime' => 
  array (
    'type' => 'class',
    'classname' => 'EventDateTime',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventDateTime',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventExtendedProperties' => 
  array (
    'type' => 'class',
    'classname' => 'EventExtendedProperties',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventExtendedProperties',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventFocusTimeProperties' => 
  array (
    'type' => 'class',
    'classname' => 'EventFocusTimeProperties',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventFocusTimeProperties',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventGadget' => 
  array (
    'type' => 'class',
    'classname' => 'EventGadget',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventGadget',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventOrganizer' => 
  array (
    'type' => 'class',
    'classname' => 'EventOrganizer',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventOrganizer',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventOutOfOfficeProperties' => 
  array (
    'type' => 'class',
    'classname' => 'EventOutOfOfficeProperties',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventOutOfOfficeProperties',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventReminder' => 
  array (
    'type' => 'class',
    'classname' => 'EventReminder',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventReminder',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventReminders' => 
  array (
    'type' => 'class',
    'classname' => 'EventReminders',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventReminders',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventSource' => 
  array (
    'type' => 'class',
    'classname' => 'EventSource',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventSource',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventWorkingLocationProperties' => 
  array (
    'type' => 'class',
    'classname' => 'EventWorkingLocationProperties',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventWorkingLocationProperties',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventWorkingLocationPropertiesCustomLocation' => 
  array (
    'type' => 'class',
    'classname' => 'EventWorkingLocationPropertiesCustomLocation',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventWorkingLocationPropertiesCustomLocation',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\EventWorkingLocationPropertiesOfficeLocation' => 
  array (
    'type' => 'class',
    'classname' => 'EventWorkingLocationPropertiesOfficeLocation',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\EventWorkingLocationPropertiesOfficeLocation',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Events' => 
  array (
    'type' => 'class',
    'classname' => 'Events',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Events',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\FreeBusyCalendar' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyCalendar',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\FreeBusyCalendar',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\FreeBusyGroup' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyGroup',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\FreeBusyGroup',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\FreeBusyRequest' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyRequest',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\FreeBusyRequest',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\FreeBusyRequestItem' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyRequestItem',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\FreeBusyRequestItem',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\FreeBusyResponse' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyResponse',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\FreeBusyResponse',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Acl' => 
  array (
    'type' => 'class',
    'classname' => 'Acl',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Acl',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\CalendarList' => 
  array (
    'type' => 'class',
    'classname' => 'CalendarList',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\CalendarList',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Calendars' => 
  array (
    'type' => 'class',
    'classname' => 'Calendars',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Calendars',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Channels' => 
  array (
    'type' => 'class',
    'classname' => 'Channels',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Channels',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Colors' => 
  array (
    'type' => 'class',
    'classname' => 'Colors',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Colors',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Events' => 
  array (
    'type' => 'class',
    'classname' => 'Events',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Events',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Freebusy' => 
  array (
    'type' => 'class',
    'classname' => 'Freebusy',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Freebusy',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Resource\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar\\Resource',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Resource\\Settings',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Setting' => 
  array (
    'type' => 'class',
    'classname' => 'Setting',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Setting',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\Settings',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Calendar\\TimePeriod' => 
  array (
    'type' => 'class',
    'classname' => 'TimePeriod',
    'isabstract' => false,
    'namespace' => 'Google\\Service\\Calendar',
    'extends' => 'AmeliaVendor\\Google\\Service\\Calendar\\TimePeriod',
    'implements' => 
    array (
    ),
  ),
  'Google\\AccessToken\\Revoke' => 
  array (
    'type' => 'class',
    'classname' => 'Revoke',
    'isabstract' => false,
    'namespace' => 'Google\\AccessToken',
    'extends' => 'AmeliaVendor\\Google\\AccessToken\\Revoke',
    'implements' => 
    array (
    ),
  ),
  'Google\\AccessToken\\Verify' => 
  array (
    'type' => 'class',
    'classname' => 'Verify',
    'isabstract' => false,
    'namespace' => 'Google\\AccessToken',
    'extends' => 'AmeliaVendor\\Google\\AccessToken\\Verify',
    'implements' => 
    array (
    ),
  ),
  'Google\\AuthHandler\\AuthHandlerFactory' => 
  array (
    'type' => 'class',
    'classname' => 'AuthHandlerFactory',
    'isabstract' => false,
    'namespace' => 'Google\\AuthHandler',
    'extends' => 'AmeliaVendor\\Google\\AuthHandler\\AuthHandlerFactory',
    'implements' => 
    array (
    ),
  ),
  'Google\\AuthHandler\\Guzzle6AuthHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Guzzle6AuthHandler',
    'isabstract' => false,
    'namespace' => 'Google\\AuthHandler',
    'extends' => 'AmeliaVendor\\Google\\AuthHandler\\Guzzle6AuthHandler',
    'implements' => 
    array (
    ),
  ),
  'Google\\AuthHandler\\Guzzle7AuthHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Guzzle7AuthHandler',
    'isabstract' => false,
    'namespace' => 'Google\\AuthHandler',
    'extends' => 'AmeliaVendor\\Google\\AuthHandler\\Guzzle7AuthHandler',
    'implements' => 
    array (
    ),
  ),
  'Google\\Client' => 
  array (
    'type' => 'class',
    'classname' => 'Client',
    'isabstract' => false,
    'namespace' => 'Google',
    'extends' => 'AmeliaVendor\\Google\\Client',
    'implements' => 
    array (
    ),
  ),
  'Google\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'Google',
    'extends' => 'AmeliaVendor\\Google\\Collection',
    'implements' => 
    array (
      0 => 'Iterator',
      1 => 'Countable',
    ),
  ),
  'Google\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Google',
    'extends' => 'AmeliaVendor\\Google\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Google\\Http\\Batch' => 
  array (
    'type' => 'class',
    'classname' => 'Batch',
    'isabstract' => false,
    'namespace' => 'Google\\Http',
    'extends' => 'AmeliaVendor\\Google\\Http\\Batch',
    'implements' => 
    array (
    ),
  ),
  'Google\\Http\\MediaFileUpload' => 
  array (
    'type' => 'class',
    'classname' => 'MediaFileUpload',
    'isabstract' => false,
    'namespace' => 'Google\\Http',
    'extends' => 'AmeliaVendor\\Google\\Http\\MediaFileUpload',
    'implements' => 
    array (
    ),
  ),
  'Google\\Http\\REST' => 
  array (
    'type' => 'class',
    'classname' => 'REST',
    'isabstract' => false,
    'namespace' => 'Google\\Http',
    'extends' => 'AmeliaVendor\\Google\\Http\\REST',
    'implements' => 
    array (
    ),
  ),
  'Google\\Model' => 
  array (
    'type' => 'class',
    'classname' => 'Model',
    'isabstract' => false,
    'namespace' => 'Google',
    'extends' => 'AmeliaVendor\\Google\\Model',
    'implements' => 
    array (
      0 => 'ArrayAccess',
    ),
  ),
  'Google\\Service\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Google\\Service',
    'extends' => 'AmeliaVendor\\Google\\Service\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Google\\Service\\Resource' => 
  array (
    'type' => 'class',
    'classname' => 'Resource',
    'isabstract' => false,
    'namespace' => 'Google\\Service',
    'extends' => 'AmeliaVendor\\Google\\Service\\Resource',
    'implements' => 
    array (
    ),
  ),
  'Google\\Task\\Composer' => 
  array (
    'type' => 'class',
    'classname' => 'Composer',
    'isabstract' => false,
    'namespace' => 'Google\\Task',
    'extends' => 'AmeliaVendor\\Google\\Task\\Composer',
    'implements' => 
    array (
    ),
  ),
  'Google\\Task\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Google\\Task',
    'extends' => 'AmeliaVendor\\Google\\Task\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Google\\Task\\Runner' => 
  array (
    'type' => 'class',
    'classname' => 'Runner',
    'isabstract' => false,
    'namespace' => 'Google\\Task',
    'extends' => 'AmeliaVendor\\Google\\Task\\Runner',
    'implements' => 
    array (
    ),
  ),
  'Google\\Utils\\UriTemplate' => 
  array (
    'type' => 'class',
    'classname' => 'UriTemplate',
    'isabstract' => false,
    'namespace' => 'Google\\Utils',
    'extends' => 'AmeliaVendor\\Google\\Utils\\UriTemplate',
    'implements' => 
    array (
    ),
  ),
  'Google_Task_Composer' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Task_Composer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Task_Composer',
    'implements' => 
    array (
    ),
  ),
  'Google_AccessToken_Revoke' => 
  array (
    'type' => 'class',
    'classname' => 'Google_AccessToken_Revoke',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_AccessToken_Revoke',
    'implements' => 
    array (
    ),
  ),
  'Google_AccessToken_Verify' => 
  array (
    'type' => 'class',
    'classname' => 'Google_AccessToken_Verify',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_AccessToken_Verify',
    'implements' => 
    array (
    ),
  ),
  'Google_AuthHandler_AuthHandlerFactory' => 
  array (
    'type' => 'class',
    'classname' => 'Google_AuthHandler_AuthHandlerFactory',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_AuthHandler_AuthHandlerFactory',
    'implements' => 
    array (
    ),
  ),
  'Google_AuthHandler_Guzzle6AuthHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Google_AuthHandler_Guzzle6AuthHandler',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_AuthHandler_Guzzle6AuthHandler',
    'implements' => 
    array (
    ),
  ),
  'Google_AuthHandler_Guzzle7AuthHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Google_AuthHandler_Guzzle7AuthHandler',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_AuthHandler_Guzzle7AuthHandler',
    'implements' => 
    array (
    ),
  ),
  'Google_Client' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Client',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Client',
    'implements' => 
    array (
    ),
  ),
  'Google_Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Collection',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Collection',
    'implements' => 
    array (
    ),
  ),
  'Google_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Exception',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Exception',
    'implements' => 
    array (
    ),
  ),
  'Google_Http_Batch' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Http_Batch',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Http_Batch',
    'implements' => 
    array (
    ),
  ),
  'Google_Http_MediaFileUpload' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Http_MediaFileUpload',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Http_MediaFileUpload',
    'implements' => 
    array (
    ),
  ),
  'Google_Http_REST' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Http_REST',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Http_REST',
    'implements' => 
    array (
    ),
  ),
  'Google_Model' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Model',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Model',
    'implements' => 
    array (
    ),
  ),
  'Google_Service' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Service',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Service',
    'implements' => 
    array (
    ),
  ),
  'Google_Service_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Service_Exception',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Service_Exception',
    'implements' => 
    array (
    ),
  ),
  'Google_Service_Resource' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Service_Resource',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Service_Resource',
    'implements' => 
    array (
    ),
  ),
  'Google_Task_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Task_Exception',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Task_Exception',
    'implements' => 
    array (
    ),
  ),
  'Google_Task_Runner' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Task_Runner',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Task_Runner',
    'implements' => 
    array (
    ),
  ),
  'Google_Utils_UriTemplate' => 
  array (
    'type' => 'class',
    'classname' => 'Google_Utils_UriTemplate',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Google_Utils_UriTemplate',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\AccessToken' => 
  array (
    'type' => 'class',
    'classname' => 'AccessToken',
    'isabstract' => false,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\AccessToken',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\Cache\\InvalidArgumentException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidArgumentException',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Cache',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Cache\\InvalidArgumentException',
    'implements' => 
    array (
      0 => 'Psr\\Cache\\InvalidArgumentException',
    ),
  ),
  'Google\\Auth\\Cache\\Item' => 
  array (
    'type' => 'class',
    'classname' => 'Item',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Cache',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Cache\\Item',
    'implements' => 
    array (
      0 => 'Psr\\Cache\\CacheItemInterface',
    ),
  ),
  'Google\\Auth\\Cache\\MemoryCacheItemPool' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryCacheItemPool',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Cache',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Cache\\MemoryCacheItemPool',
    'implements' => 
    array (
      0 => 'Psr\\Cache\\CacheItemPoolInterface',
    ),
  ),
  'Google\\Auth\\Cache\\SysVCacheItemPool' => 
  array (
    'type' => 'class',
    'classname' => 'SysVCacheItemPool',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Cache',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Cache\\SysVCacheItemPool',
    'implements' => 
    array (
      0 => 'Psr\\Cache\\CacheItemPoolInterface',
    ),
  ),
  'Google\\Auth\\Cache\\TypedItem' => 
  array (
    'type' => 'class',
    'classname' => 'TypedItem',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Cache',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Cache\\TypedItem',
    'implements' => 
    array (
      0 => 'Psr\\Cache\\CacheItemInterface',
    ),
  ),
  'Google\\Auth\\CredentialSource\\AwsNativeSource' => 
  array (
    'type' => 'class',
    'classname' => 'AwsNativeSource',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\CredentialSource',
    'extends' => 'AmeliaVendor\\Google\\Auth\\CredentialSource\\AwsNativeSource',
    'implements' => 
    array (
      0 => 'Google\\Auth\\ExternalAccountCredentialSourceInterface',
    ),
  ),
  'Google\\Auth\\CredentialSource\\FileSource' => 
  array (
    'type' => 'class',
    'classname' => 'FileSource',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\CredentialSource',
    'extends' => 'AmeliaVendor\\Google\\Auth\\CredentialSource\\FileSource',
    'implements' => 
    array (
      0 => 'Google\\Auth\\ExternalAccountCredentialSourceInterface',
    ),
  ),
  'Google\\Auth\\CredentialSource\\UrlSource' => 
  array (
    'type' => 'class',
    'classname' => 'UrlSource',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\CredentialSource',
    'extends' => 'AmeliaVendor\\Google\\Auth\\CredentialSource\\UrlSource',
    'implements' => 
    array (
      0 => 'Google\\Auth\\ExternalAccountCredentialSourceInterface',
    ),
  ),
  'Google\\Auth\\Credentials\\ExternalAccountCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'ExternalAccountCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\ExternalAccountCredentials',
    'implements' => 
    array (
      0 => 'Google\\Auth\\FetchAuthTokenInterface',
      1 => 'Google\\Auth\\UpdateMetadataInterface',
      2 => 'Google\\Auth\\GetQuotaProjectInterface',
      3 => 'Google\\Auth\\GetUniverseDomainInterface',
      4 => 'Google\\Auth\\ProjectIdProviderInterface',
    ),
  ),
  'Google\\Auth\\Credentials\\IAMCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'IAMCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\IAMCredentials',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\Credentials\\ImpersonatedServiceAccountCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'ImpersonatedServiceAccountCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\ImpersonatedServiceAccountCredentials',
    'implements' => 
    array (
      0 => 'Google\\Auth\\SignBlobInterface',
    ),
  ),
  'Google\\Auth\\Credentials\\InsecureCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'InsecureCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\InsecureCredentials',
    'implements' => 
    array (
      0 => 'Google\\Auth\\FetchAuthTokenInterface',
    ),
  ),
  'Google\\Auth\\Credentials\\ServiceAccountJwtAccessCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'ServiceAccountJwtAccessCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\ServiceAccountJwtAccessCredentials',
    'implements' => 
    array (
      0 => 'Google\\Auth\\GetQuotaProjectInterface',
      1 => 'Google\\Auth\\SignBlobInterface',
      2 => 'Google\\Auth\\ProjectIdProviderInterface',
    ),
  ),
  'Google\\Auth\\Credentials\\UserRefreshCredentials' => 
  array (
    'type' => 'class',
    'classname' => 'UserRefreshCredentials',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\Credentials',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Credentials\\UserRefreshCredentials',
    'implements' => 
    array (
      0 => 'Google\\Auth\\GetQuotaProjectInterface',
    ),
  ),
  'Google\\Auth\\CredentialsLoader' => 
  array (
    'type' => 'class',
    'classname' => 'CredentialsLoader',
    'isabstract' => true,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\CredentialsLoader',
    'implements' => 
    array (
      0 => 'Google\\Auth\\GetUniverseDomainInterface',
      1 => 'Google\\Auth\\FetchAuthTokenInterface',
      2 => 'Google\\Auth\\UpdateMetadataInterface',
    ),
  ),
  'Google\\Auth\\FetchAuthTokenCache' => 
  array (
    'type' => 'class',
    'classname' => 'FetchAuthTokenCache',
    'isabstract' => false,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\FetchAuthTokenCache',
    'implements' => 
    array (
      0 => 'Google\\Auth\\FetchAuthTokenInterface',
      1 => 'Google\\Auth\\GetQuotaProjectInterface',
      2 => 'Google\\Auth\\GetUniverseDomainInterface',
      3 => 'Google\\Auth\\SignBlobInterface',
      4 => 'Google\\Auth\\ProjectIdProviderInterface',
      5 => 'Google\\Auth\\UpdateMetadataInterface',
    ),
  ),
  'Google\\Auth\\GCECache' => 
  array (
    'type' => 'class',
    'classname' => 'GCECache',
    'isabstract' => false,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\GCECache',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\HttpHandler\\Guzzle6HttpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Guzzle6HttpHandler',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\HttpHandler',
    'extends' => 'AmeliaVendor\\Google\\Auth\\HttpHandler\\Guzzle6HttpHandler',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\HttpHandler\\Guzzle7HttpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'Guzzle7HttpHandler',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\HttpHandler',
    'extends' => 'AmeliaVendor\\Google\\Auth\\HttpHandler\\Guzzle7HttpHandler',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\HttpHandler\\HttpClientCache' => 
  array (
    'type' => 'class',
    'classname' => 'HttpClientCache',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\HttpHandler',
    'extends' => 'AmeliaVendor\\Google\\Auth\\HttpHandler\\HttpClientCache',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\HttpHandler\\HttpHandlerFactory' => 
  array (
    'type' => 'class',
    'classname' => 'HttpHandlerFactory',
    'isabstract' => false,
    'namespace' => 'Google\\Auth\\HttpHandler',
    'extends' => 'AmeliaVendor\\Google\\Auth\\HttpHandler\\HttpHandlerFactory',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\Iam' => 
  array (
    'type' => 'class',
    'classname' => 'Iam',
    'isabstract' => false,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\Iam',
    'implements' => 
    array (
    ),
  ),
  'Google\\Auth\\OAuth2' => 
  array (
    'type' => 'class',
    'classname' => 'OAuth2',
    'isabstract' => false,
    'namespace' => 'Google\\Auth',
    'extends' => 'AmeliaVendor\\Google\\Auth\\OAuth2',
    'implements' => 
    array (
      0 => 'Google\\Auth\\FetchAuthTokenInterface',
    ),
  ),
  'Masterminds\\HTML5\\Elements' => 
  array (
    'type' => 'class',
    'classname' => 'Elements',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Elements',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Entities' => 
  array (
    'type' => 'class',
    'classname' => 'Entities',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Entities',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\CharacterReference' => 
  array (
    'type' => 'class',
    'classname' => 'CharacterReference',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\CharacterReference',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\DOMTreeBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'DOMTreeBuilder',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\DOMTreeBuilder',
    'implements' => 
    array (
      0 => 'Masterminds\\HTML5\\Parser\\EventHandler',
    ),
  ),
  'Masterminds\\HTML5\\Parser\\FileInputStream' => 
  array (
    'type' => 'class',
    'classname' => 'FileInputStream',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\FileInputStream',
    'implements' => 
    array (
      0 => 'Masterminds\\HTML5\\Parser\\InputStream',
    ),
  ),
  'Masterminds\\HTML5\\Parser\\ParseError' => 
  array (
    'type' => 'class',
    'classname' => 'ParseError',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\ParseError',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\Scanner' => 
  array (
    'type' => 'class',
    'classname' => 'Scanner',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\Scanner',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\StringInputStream' => 
  array (
    'type' => 'class',
    'classname' => 'StringInputStream',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\StringInputStream',
    'implements' => 
    array (
      0 => 'Masterminds\\HTML5\\Parser\\InputStream',
    ),
  ),
  'Masterminds\\HTML5\\Parser\\Tokenizer' => 
  array (
    'type' => 'class',
    'classname' => 'Tokenizer',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\Tokenizer',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\TreeBuildingRules' => 
  array (
    'type' => 'class',
    'classname' => 'TreeBuildingRules',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\TreeBuildingRules',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Parser\\UTF8Utils' => 
  array (
    'type' => 'class',
    'classname' => 'UTF8Utils',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\UTF8Utils',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Serializer\\HTML5Entities' => 
  array (
    'type' => 'class',
    'classname' => 'HTML5Entities',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Serializer',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Serializer\\HTML5Entities',
    'implements' => 
    array (
    ),
  ),
  'Masterminds\\HTML5\\Serializer\\OutputRules' => 
  array (
    'type' => 'class',
    'classname' => 'OutputRules',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Serializer',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Serializer\\OutputRules',
    'implements' => 
    array (
      0 => 'Masterminds\\HTML5\\Serializer\\RulesInterface',
    ),
  ),
  'Masterminds\\HTML5\\Serializer\\Traverser' => 
  array (
    'type' => 'class',
    'classname' => 'Traverser',
    'isabstract' => false,
    'namespace' => 'Masterminds\\HTML5\\Serializer',
    'extends' => 'AmeliaVendor\\Masterminds\\HTML5\\Serializer\\Traverser',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Attribute\\AsMonologProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'AsMonologProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Attribute',
    'extends' => 'AmeliaVendor\\Monolog\\Attribute\\AsMonologProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\DateTimeImmutable' => 
  array (
    'type' => 'class',
    'classname' => 'DateTimeImmutable',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\DateTimeImmutable',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'Monolog\\ErrorHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorHandler',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\ErrorHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\ChromePHPFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ChromePHPFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\ChromePHPFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\ElasticaFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticaFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\ElasticaFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\ElasticsearchFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticsearchFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\ElasticsearchFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\FlowdockFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'FlowdockFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\FlowdockFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\FluentdFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'FluentdFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\FluentdFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\GelfMessageFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'GelfMessageFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\GelfMessageFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\GoogleCloudLoggingFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'GoogleCloudLoggingFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\GoogleCloudLoggingFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\HtmlFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'HtmlFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\HtmlFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\JsonFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'JsonFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\JsonFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LineFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LineFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\LineFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogglyFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogglyFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\LogglyFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogmaticFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogmaticFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\LogmaticFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\LogstashFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'LogstashFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\LogstashFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\MongoDBFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'MongoDBFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\MongoDBFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\NormalizerFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'NormalizerFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\NormalizerFormatter',
    'implements' => 
    array (
      0 => 'Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Formatter\\ScalarFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'ScalarFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\ScalarFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Formatter\\WildfireFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'WildfireFormatter',
    'isabstract' => false,
    'namespace' => 'Monolog\\Formatter',
    'extends' => 'AmeliaVendor\\Monolog\\Formatter\\WildfireFormatter',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\AbstractHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\AbstractHandler',
    'implements' => 
    array (
      0 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\AbstractProcessingHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractProcessingHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\AbstractProcessingHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\AbstractSyslogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractSyslogHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\AbstractSyslogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\AmqpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'AmqpHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\AmqpHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\BrowserConsoleHandler' => 
  array (
    'type' => 'class',
    'classname' => 'BrowserConsoleHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\BrowserConsoleHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\BufferHandler' => 
  array (
    'type' => 'class',
    'classname' => 'BufferHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\BufferHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\ChromePHPHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ChromePHPHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ChromePHPHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\CouchDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'CouchDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\CouchDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\CubeHandler' => 
  array (
    'type' => 'class',
    'classname' => 'CubeHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\CubeHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\Curl\\Util' => 
  array (
    'type' => 'class',
    'classname' => 'Util',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\Curl',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\Curl\\Util',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DeduplicationHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DeduplicationHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\DeduplicationHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DoctrineCouchDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DoctrineCouchDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\DoctrineCouchDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\DynamoDbHandler' => 
  array (
    'type' => 'class',
    'classname' => 'DynamoDbHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\DynamoDbHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ElasticaHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticaHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ElasticaHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ElasticsearchHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ElasticsearchHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ElasticsearchHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ErrorLogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorLogHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ErrorLogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FallbackGroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FallbackGroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FallbackGroupHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FilterHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FilterHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FilterHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy' => 
  array (
    'type' => 'class',
    'classname' => 'ChannelLevelActivationStrategy',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FingersCrossed\\ChannelLevelActivationStrategy',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorLevelActivationStrategy',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FingersCrossed\\ErrorLevelActivationStrategy',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossedHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FingersCrossedHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FingersCrossedHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\FirePHPHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FirePHPHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FirePHPHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FleepHookHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FleepHookHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FleepHookHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\FlowdockHandler' => 
  array (
    'type' => 'class',
    'classname' => 'FlowdockHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\FlowdockHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\GelfHandler' => 
  array (
    'type' => 'class',
    'classname' => 'GelfHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\GelfHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\GroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'GroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\GroupHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\Handler' => 
  array (
    'type' => 'class',
    'classname' => 'Handler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\Handler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\HandlerInterface',
    ),
  ),
  'Monolog\\Handler\\HandlerWrapper' => 
  array (
    'type' => 'class',
    'classname' => 'HandlerWrapper',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\HandlerWrapper',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\HandlerInterface',
      1 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      2 => 'Monolog\\Handler\\FormattableHandlerInterface',
      3 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Handler\\IFTTTHandler' => 
  array (
    'type' => 'class',
    'classname' => 'IFTTTHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\IFTTTHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\InsightOpsHandler' => 
  array (
    'type' => 'class',
    'classname' => 'InsightOpsHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\InsightOpsHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogEntriesHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogEntriesHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\LogEntriesHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogglyHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogglyHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\LogglyHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\LogmaticHandler' => 
  array (
    'type' => 'class',
    'classname' => 'LogmaticHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\LogmaticHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MailHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MailHandler',
    'isabstract' => true,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\MailHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MandrillHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MandrillHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\MandrillHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MissingExtensionException' => 
  array (
    'type' => 'class',
    'classname' => 'MissingExtensionException',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\MissingExtensionException',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\MongoDBHandler' => 
  array (
    'type' => 'class',
    'classname' => 'MongoDBHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\MongoDBHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NativeMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NativeMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\NativeMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NewRelicHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NewRelicHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\NewRelicHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NoopHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NoopHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\NoopHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\NullHandler' => 
  array (
    'type' => 'class',
    'classname' => 'NullHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\NullHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\OverflowHandler' => 
  array (
    'type' => 'class',
    'classname' => 'OverflowHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\OverflowHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\PHPConsoleHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PHPConsoleHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\PHPConsoleHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ProcessHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ProcessHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ProcessHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\PsrHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PsrHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\PsrHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\PushoverHandler' => 
  array (
    'type' => 'class',
    'classname' => 'PushoverHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\PushoverHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RedisHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RedisHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\RedisHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RedisPubSubHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RedisPubSubHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\RedisPubSubHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RollbarHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RollbarHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\RollbarHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\RotatingFileHandler' => 
  array (
    'type' => 'class',
    'classname' => 'RotatingFileHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\RotatingFileHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SamplingHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SamplingHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SamplingHandler',
    'implements' => 
    array (
      0 => 'Monolog\\Handler\\ProcessableHandlerInterface',
      1 => 'Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\SendGridHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SendGridHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SendGridHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\Slack\\SlackRecord' => 
  array (
    'type' => 'class',
    'classname' => 'SlackRecord',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\Slack',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\Slack\\SlackRecord',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SlackHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SlackHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SlackHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SlackWebhookHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SlackWebhookHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SlackWebhookHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SocketHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SocketHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SocketHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SqsHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SqsHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SqsHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\StreamHandler' => 
  array (
    'type' => 'class',
    'classname' => 'StreamHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\StreamHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SwiftMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SwiftMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SwiftMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SymfonyMailerHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SymfonyMailerHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SymfonyMailerHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SyslogHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SyslogHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogUdp\\UdpSocket' => 
  array (
    'type' => 'class',
    'classname' => 'UdpSocket',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler\\SyslogUdp',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SyslogUdp\\UdpSocket',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\SyslogUdpHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SyslogUdpHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\SyslogUdpHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\TelegramBotHandler' => 
  array (
    'type' => 'class',
    'classname' => 'TelegramBotHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\TelegramBotHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\TestHandler' => 
  array (
    'type' => 'class',
    'classname' => 'TestHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\TestHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\WhatFailureGroupHandler' => 
  array (
    'type' => 'class',
    'classname' => 'WhatFailureGroupHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\WhatFailureGroupHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Handler\\ZendMonitorHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ZendMonitorHandler',
    'isabstract' => false,
    'namespace' => 'Monolog\\Handler',
    'extends' => 'AmeliaVendor\\Monolog\\Handler\\ZendMonitorHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Logger' => 
  array (
    'type' => 'class',
    'classname' => 'Logger',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\Logger',
    'implements' => 
    array (
      0 => 'Psr\\Log\\LoggerInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Processor\\GitProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'GitProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\GitProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\HostnameProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'HostnameProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\HostnameProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\IntrospectionProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'IntrospectionProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\IntrospectionProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\MemoryPeakUsageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryPeakUsageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\MemoryPeakUsageProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Processor\\MemoryProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryProcessor',
    'isabstract' => true,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\MemoryProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\MemoryUsageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryUsageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\MemoryUsageProcessor',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Processor\\MercurialProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'MercurialProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\MercurialProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\ProcessIdProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'ProcessIdProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\ProcessIdProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\PsrLogMessageProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'PsrLogMessageProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\PsrLogMessageProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\TagProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'TagProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\TagProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Processor\\UidProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'UidProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\UidProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
      1 => 'Monolog\\ResettableInterface',
    ),
  ),
  'Monolog\\Processor\\WebProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'WebProcessor',
    'isabstract' => false,
    'namespace' => 'Monolog\\Processor',
    'extends' => 'AmeliaVendor\\Monolog\\Processor\\WebProcessor',
    'implements' => 
    array (
      0 => 'Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\Registry' => 
  array (
    'type' => 'class',
    'classname' => 'Registry',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\Registry',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\SignalHandler' => 
  array (
    'type' => 'class',
    'classname' => 'SignalHandler',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\SignalHandler',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Test\\TestCase' => 
  array (
    'type' => 'class',
    'classname' => 'TestCase',
    'isabstract' => false,
    'namespace' => 'Monolog\\Test',
    'extends' => 'AmeliaVendor\\Monolog\\Test\\TestCase',
    'implements' => 
    array (
    ),
  ),
  'Monolog\\Utils' => 
  array (
    'type' => 'class',
    'classname' => 'Utils',
    'isabstract' => false,
    'namespace' => 'Monolog',
    'extends' => 'AmeliaVendor\\Monolog\\Utils',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Base32' => 
  array (
    'type' => 'class',
    'classname' => 'Base32',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base32',
    'implements' => 
    array (
      0 => 'ParagonIE\\ConstantTime\\EncoderInterface',
    ),
  ),
  'ParagonIE\\ConstantTime\\Base32Hex' => 
  array (
    'type' => 'class',
    'classname' => 'Base32Hex',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base32Hex',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Base64' => 
  array (
    'type' => 'class',
    'classname' => 'Base64',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base64',
    'implements' => 
    array (
      0 => 'ParagonIE\\ConstantTime\\EncoderInterface',
    ),
  ),
  'ParagonIE\\ConstantTime\\Base64DotSlash' => 
  array (
    'type' => 'class',
    'classname' => 'Base64DotSlash',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base64DotSlash',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Base64DotSlashOrdered' => 
  array (
    'type' => 'class',
    'classname' => 'Base64DotSlashOrdered',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base64DotSlashOrdered',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Base64UrlSafe' => 
  array (
    'type' => 'class',
    'classname' => 'Base64UrlSafe',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Base64UrlSafe',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Binary' => 
  array (
    'type' => 'class',
    'classname' => 'Binary',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Binary',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Encoding' => 
  array (
    'type' => 'class',
    'classname' => 'Encoding',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Encoding',
    'implements' => 
    array (
    ),
  ),
  'ParagonIE\\ConstantTime\\Hex' => 
  array (
    'type' => 'class',
    'classname' => 'Hex',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\Hex',
    'implements' => 
    array (
      0 => 'ParagonIE\\ConstantTime\\EncoderInterface',
    ),
  ),
  'ParagonIE\\ConstantTime\\RFC4648' => 
  array (
    'type' => 'class',
    'classname' => 'RFC4648',
    'isabstract' => true,
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 'AmeliaVendor\\ParagonIE\\ConstantTime\\RFC4648',
    'implements' => 
    array (
    ),
  ),
  'PHPMailer\\PHPMailer\\DSNConfigurator' => 
  array (
    'type' => 'class',
    'classname' => 'DSNConfigurator',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\DSNConfigurator',
    'implements' => 
    array (
    ),
  ),
  'PHPMailer\\PHPMailer\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PHPMailer\\PHPMailer\\OAuth' => 
  array (
    'type' => 'class',
    'classname' => 'OAuth',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\OAuth',
    'implements' => 
    array (
      0 => 'PHPMailer\\PHPMailer\\OAuthTokenProvider',
    ),
  ),
  'PHPMailer\\PHPMailer\\PHPMailer' => 
  array (
    'type' => 'class',
    'classname' => 'PHPMailer',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\PHPMailer',
    'implements' => 
    array (
    ),
  ),
  'PHPMailer\\PHPMailer\\POP3' => 
  array (
    'type' => 'class',
    'classname' => 'POP3',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\POP3',
    'implements' => 
    array (
    ),
  ),
  'PHPMailer\\PHPMailer\\SMTP' => 
  array (
    'type' => 'class',
    'classname' => 'SMTP',
    'isabstract' => false,
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 'AmeliaVendor\\PHPMailer\\PHPMailer\\SMTP',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Common\\Functions\\Strings' => 
  array (
    'type' => 'class',
    'classname' => 'Strings',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Common\\Functions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Common\\Functions\\Strings',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\AES' => 
  array (
    'type' => 'class',
    'classname' => 'AES',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\AES',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Blowfish' => 
  array (
    'type' => 'class',
    'classname' => 'Blowfish',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Blowfish',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\ChaCha20' => 
  array (
    'type' => 'class',
    'classname' => 'ChaCha20',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\ChaCha20',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\AsymmetricKey' => 
  array (
    'type' => 'class',
    'classname' => 'AsymmetricKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\AsymmetricKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\BlockCipher' => 
  array (
    'type' => 'class',
    'classname' => 'BlockCipher',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\BlockCipher',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\JWK' => 
  array (
    'type' => 'class',
    'classname' => 'JWK',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\JWK',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\OpenSSH' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSH',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\OpenSSH',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS1' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS8' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS8',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\PKCS8',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Keys\\PuTTY' => 
  array (
    'type' => 'class',
    'classname' => 'PuTTY',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Keys\\PuTTY',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Formats\\Signature\\Raw' => 
  array (
    'type' => 'class',
    'classname' => 'Raw',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Formats\\Signature\\Raw',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\StreamCipher' => 
  array (
    'type' => 'class',
    'classname' => 'StreamCipher',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\StreamCipher',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Common\\SymmetricKey' => 
  array (
    'type' => 'class',
    'classname' => 'SymmetricKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\SymmetricKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DES' => 
  array (
    'type' => 'class',
    'classname' => 'DES',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DES',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DH\\Formats\\Keys\\PKCS1' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DH\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DH\\Formats\\Keys\\PKCS1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DH\\Formats\\Keys\\PKCS8' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS8',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DH\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DH\\Formats\\Keys\\PKCS8',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DH\\Parameters' => 
  array (
    'type' => 'class',
    'classname' => 'Parameters',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DH',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DH\\Parameters',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DH\\PrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DH',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DH\\PrivateKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DH\\PublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DH',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DH\\PublicKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\OpenSSH' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSH',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\OpenSSH',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PKCS1' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PKCS1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PKCS8' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS8',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PKCS8',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PuTTY' => 
  array (
    'type' => 'class',
    'classname' => 'PuTTY',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\PuTTY',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\Raw' => 
  array (
    'type' => 'class',
    'classname' => 'Raw',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\Raw',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Keys\\XML' => 
  array (
    'type' => 'class',
    'classname' => 'XML',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Keys\\XML',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Signature\\ASN1' => 
  array (
    'type' => 'class',
    'classname' => 'ASN1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Signature\\ASN1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Signature\\Raw' => 
  array (
    'type' => 'class',
    'classname' => 'Raw',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Signature\\Raw',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Formats\\Signature\\SSH2' => 
  array (
    'type' => 'class',
    'classname' => 'SSH2',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\DSA\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Formats\\Signature\\SSH2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\Parameters' => 
  array (
    'type' => 'class',
    'classname' => 'Parameters',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DSA',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\Parameters',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\PrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DSA',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\PrivateKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PrivateKey',
    ),
  ),
  'phpseclib3\\Crypt\\DSA\\PublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\DSA',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\DSA\\PublicKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PublicKey',
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\Base' => 
  array (
    'type' => 'class',
    'classname' => 'Base',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\Base',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\Binary' => 
  array (
    'type' => 'class',
    'classname' => 'Binary',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\Binary',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\KoblitzPrime' => 
  array (
    'type' => 'class',
    'classname' => 'KoblitzPrime',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\KoblitzPrime',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\Montgomery' => 
  array (
    'type' => 'class',
    'classname' => 'Montgomery',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\Montgomery',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\Prime' => 
  array (
    'type' => 'class',
    'classname' => 'Prime',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\Prime',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\BaseCurves\\TwistedEdwards' => 
  array (
    'type' => 'class',
    'classname' => 'TwistedEdwards',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\BaseCurves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\BaseCurves\\TwistedEdwards',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\Curve25519' => 
  array (
    'type' => 'class',
    'classname' => 'Curve25519',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\Curve25519',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\Curve448' => 
  array (
    'type' => 'class',
    'classname' => 'Curve448',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\Curve448',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\Ed25519' => 
  array (
    'type' => 'class',
    'classname' => 'Ed25519',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\Ed25519',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\Ed448' => 
  array (
    'type' => 'class',
    'classname' => 'Ed448',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\Ed448',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP160r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP160r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP160r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP160t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP160t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP160t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP192r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP192r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP192r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP192t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP192t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP192t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP224r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP224r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP224r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP224t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP224t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP224t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP256r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP256r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP256r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP256t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP256t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP256t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP320r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP320r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP320r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP320t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP320t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP320t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP384r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP384r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP384r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP384t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP384t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP384t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP512r1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP512r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP512r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\brainpoolP512t1' => 
  array (
    'type' => 'class',
    'classname' => 'brainpoolP512t1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\brainpoolP512t1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistb233' => 
  array (
    'type' => 'class',
    'classname' => 'nistb233',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistb233',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistb409' => 
  array (
    'type' => 'class',
    'classname' => 'nistb409',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistb409',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistk163' => 
  array (
    'type' => 'class',
    'classname' => 'nistk163',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistk163',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistk233' => 
  array (
    'type' => 'class',
    'classname' => 'nistk233',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistk233',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistk283' => 
  array (
    'type' => 'class',
    'classname' => 'nistk283',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistk283',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistk409' => 
  array (
    'type' => 'class',
    'classname' => 'nistk409',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistk409',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistp192' => 
  array (
    'type' => 'class',
    'classname' => 'nistp192',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistp192',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistp224' => 
  array (
    'type' => 'class',
    'classname' => 'nistp224',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistp224',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistp256' => 
  array (
    'type' => 'class',
    'classname' => 'nistp256',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistp256',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistp384' => 
  array (
    'type' => 'class',
    'classname' => 'nistp384',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistp384',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistp521' => 
  array (
    'type' => 'class',
    'classname' => 'nistp521',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistp521',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\nistt571' => 
  array (
    'type' => 'class',
    'classname' => 'nistt571',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\nistt571',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime192v1' => 
  array (
    'type' => 'class',
    'classname' => 'prime192v1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime192v1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime192v2' => 
  array (
    'type' => 'class',
    'classname' => 'prime192v2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime192v2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime192v3' => 
  array (
    'type' => 'class',
    'classname' => 'prime192v3',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime192v3',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime239v1' => 
  array (
    'type' => 'class',
    'classname' => 'prime239v1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime239v1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime239v2' => 
  array (
    'type' => 'class',
    'classname' => 'prime239v2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime239v2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime239v3' => 
  array (
    'type' => 'class',
    'classname' => 'prime239v3',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime239v3',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\prime256v1' => 
  array (
    'type' => 'class',
    'classname' => 'prime256v1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\prime256v1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp112r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp112r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp112r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp112r2' => 
  array (
    'type' => 'class',
    'classname' => 'secp112r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp112r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp128r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp128r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp128r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp128r2' => 
  array (
    'type' => 'class',
    'classname' => 'secp128r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp128r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp160k1' => 
  array (
    'type' => 'class',
    'classname' => 'secp160k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp160k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp160r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp160r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp160r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp160r2' => 
  array (
    'type' => 'class',
    'classname' => 'secp160r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp160r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp192k1' => 
  array (
    'type' => 'class',
    'classname' => 'secp192k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp192k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp192r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp192r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp192r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp224k1' => 
  array (
    'type' => 'class',
    'classname' => 'secp224k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp224k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp224r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp224r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp224r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp256k1' => 
  array (
    'type' => 'class',
    'classname' => 'secp256k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp256k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp256r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp256r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp256r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp384r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp384r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp384r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\secp521r1' => 
  array (
    'type' => 'class',
    'classname' => 'secp521r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\secp521r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect113r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect113r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect113r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect113r2' => 
  array (
    'type' => 'class',
    'classname' => 'sect113r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect113r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect131r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect131r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect131r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect131r2' => 
  array (
    'type' => 'class',
    'classname' => 'sect131r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect131r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect163k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect163k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect163k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect163r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect163r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect163r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect163r2' => 
  array (
    'type' => 'class',
    'classname' => 'sect163r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect163r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect193r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect193r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect193r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect193r2' => 
  array (
    'type' => 'class',
    'classname' => 'sect193r2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect193r2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect233k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect233k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect233k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect233r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect233r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect233r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect239k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect239k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect239k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect283k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect283k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect283k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect283r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect283r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect283r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect409k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect409k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect409k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect409r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect409r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect409r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect571k1' => 
  array (
    'type' => 'class',
    'classname' => 'sect571k1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect571k1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Curves\\sect571r1' => 
  array (
    'type' => 'class',
    'classname' => 'sect571r1',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Curves',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Curves\\sect571r1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\JWK' => 
  array (
    'type' => 'class',
    'classname' => 'JWK',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\JWK',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\MontgomeryPrivate' => 
  array (
    'type' => 'class',
    'classname' => 'MontgomeryPrivate',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\MontgomeryPrivate',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\MontgomeryPublic' => 
  array (
    'type' => 'class',
    'classname' => 'MontgomeryPublic',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\MontgomeryPublic',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\OpenSSH' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSH',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\OpenSSH',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\PKCS1' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\PKCS1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\PKCS8' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS8',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\PKCS8',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\PuTTY' => 
  array (
    'type' => 'class',
    'classname' => 'PuTTY',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\PuTTY',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\XML' => 
  array (
    'type' => 'class',
    'classname' => 'XML',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\XML',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\libsodium' => 
  array (
    'type' => 'class',
    'classname' => 'libsodium',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\libsodium',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Signature\\ASN1' => 
  array (
    'type' => 'class',
    'classname' => 'ASN1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Signature\\ASN1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Signature\\IEEE' => 
  array (
    'type' => 'class',
    'classname' => 'IEEE',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Signature\\IEEE',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Signature\\Raw' => 
  array (
    'type' => 'class',
    'classname' => 'Raw',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Signature\\Raw',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Signature\\SSH2' => 
  array (
    'type' => 'class',
    'classname' => 'SSH2',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Signature',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Signature\\SSH2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Parameters' => 
  array (
    'type' => 'class',
    'classname' => 'Parameters',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Parameters',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\EC\\PrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\PrivateKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PrivateKey',
    ),
  ),
  'phpseclib3\\Crypt\\EC\\PublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\EC',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\PublicKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PublicKey',
    ),
  ),
  'phpseclib3\\Crypt\\Hash' => 
  array (
    'type' => 'class',
    'classname' => 'Hash',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Hash',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\PublicKeyLoader' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKeyLoader',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\PublicKeyLoader',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RC2' => 
  array (
    'type' => 'class',
    'classname' => 'RC2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RC2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RC4' => 
  array (
    'type' => 'class',
    'classname' => 'RC4',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RC4',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\JWK' => 
  array (
    'type' => 'class',
    'classname' => 'JWK',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\JWK',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\MSBLOB' => 
  array (
    'type' => 'class',
    'classname' => 'MSBLOB',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\MSBLOB',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\OpenSSH' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSH',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\OpenSSH',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PKCS1' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS1',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PKCS1',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PKCS8' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS8',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PKCS8',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PSS' => 
  array (
    'type' => 'class',
    'classname' => 'PSS',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PSS',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PuTTY' => 
  array (
    'type' => 'class',
    'classname' => 'PuTTY',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\PuTTY',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\Raw' => 
  array (
    'type' => 'class',
    'classname' => 'Raw',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\Raw',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\Formats\\Keys\\XML' => 
  array (
    'type' => 'class',
    'classname' => 'XML',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt\\RSA\\Formats\\Keys',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\Formats\\Keys\\XML',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\PrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\RSA',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\PrivateKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PrivateKey',
    ),
  ),
  'phpseclib3\\Crypt\\RSA\\PublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKey',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt\\RSA',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\RSA\\PublicKey',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PublicKey',
    ),
  ),
  'phpseclib3\\Crypt\\Random' => 
  array (
    'type' => 'class',
    'classname' => 'Random',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Random',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Rijndael' => 
  array (
    'type' => 'class',
    'classname' => 'Rijndael',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Rijndael',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Salsa20' => 
  array (
    'type' => 'class',
    'classname' => 'Salsa20',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Salsa20',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\TripleDES' => 
  array (
    'type' => 'class',
    'classname' => 'TripleDES',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\TripleDES',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Crypt\\Twofish' => 
  array (
    'type' => 'class',
    'classname' => 'Twofish',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Crypt',
    'extends' => 'AmeliaVendor\\phpseclib3\\Crypt\\Twofish',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\BadConfigurationException' => 
  array (
    'type' => 'class',
    'classname' => 'BadConfigurationException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\BadConfigurationException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\BadDecryptionException' => 
  array (
    'type' => 'class',
    'classname' => 'BadDecryptionException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\BadDecryptionException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\BadModeException' => 
  array (
    'type' => 'class',
    'classname' => 'BadModeException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\BadModeException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\ConnectionClosedException' => 
  array (
    'type' => 'class',
    'classname' => 'ConnectionClosedException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\ConnectionClosedException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\FileNotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'FileNotFoundException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\FileNotFoundException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\InconsistentSetupException' => 
  array (
    'type' => 'class',
    'classname' => 'InconsistentSetupException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\InconsistentSetupException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\InsufficientSetupException' => 
  array (
    'type' => 'class',
    'classname' => 'InsufficientSetupException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\InsufficientSetupException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\InvalidPacketLengthException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidPacketLengthException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\InvalidPacketLengthException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\NoKeyLoadedException' => 
  array (
    'type' => 'class',
    'classname' => 'NoKeyLoadedException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\NoKeyLoadedException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\NoSupportedAlgorithmsException' => 
  array (
    'type' => 'class',
    'classname' => 'NoSupportedAlgorithmsException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\NoSupportedAlgorithmsException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\TimeoutException' => 
  array (
    'type' => 'class',
    'classname' => 'TimeoutException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\TimeoutException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\UnableToConnectException' => 
  array (
    'type' => 'class',
    'classname' => 'UnableToConnectException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\UnableToConnectException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\UnsupportedAlgorithmException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedAlgorithmException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\UnsupportedAlgorithmException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\UnsupportedCurveException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedCurveException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\UnsupportedCurveException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\UnsupportedFormatException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedFormatException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\UnsupportedFormatException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Exception\\UnsupportedOperationException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedOperationException',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Exception',
    'extends' => 'AmeliaVendor\\phpseclib3\\Exception\\UnsupportedOperationException',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ANSI' => 
  array (
    'type' => 'class',
    'classname' => 'ANSI',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\File',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ANSI',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Element' => 
  array (
    'type' => 'class',
    'classname' => 'Element',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\File\\ASN1',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Element',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AccessDescription' => 
  array (
    'type' => 'class',
    'classname' => 'AccessDescription',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AccessDescription',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AdministrationDomainName' => 
  array (
    'type' => 'class',
    'classname' => 'AdministrationDomainName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AdministrationDomainName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AlgorithmIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'AlgorithmIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AlgorithmIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AnotherName' => 
  array (
    'type' => 'class',
    'classname' => 'AnotherName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AnotherName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Attribute' => 
  array (
    'type' => 'class',
    'classname' => 'Attribute',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Attribute',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AttributeType' => 
  array (
    'type' => 'class',
    'classname' => 'AttributeType',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AttributeType',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AttributeTypeAndValue' => 
  array (
    'type' => 'class',
    'classname' => 'AttributeTypeAndValue',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AttributeTypeAndValue',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AttributeValue' => 
  array (
    'type' => 'class',
    'classname' => 'AttributeValue',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AttributeValue',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Attributes' => 
  array (
    'type' => 'class',
    'classname' => 'Attributes',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Attributes',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AuthorityInfoAccessSyntax' => 
  array (
    'type' => 'class',
    'classname' => 'AuthorityInfoAccessSyntax',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AuthorityInfoAccessSyntax',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\AuthorityKeyIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'AuthorityKeyIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\AuthorityKeyIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\BaseDistance' => 
  array (
    'type' => 'class',
    'classname' => 'BaseDistance',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\BaseDistance',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\BasicConstraints' => 
  array (
    'type' => 'class',
    'classname' => 'BasicConstraints',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\BasicConstraints',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\BuiltInDomainDefinedAttribute' => 
  array (
    'type' => 'class',
    'classname' => 'BuiltInDomainDefinedAttribute',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\BuiltInDomainDefinedAttribute',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\BuiltInDomainDefinedAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'BuiltInDomainDefinedAttributes',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\BuiltInDomainDefinedAttributes',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\BuiltInStandardAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'BuiltInStandardAttributes',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\BuiltInStandardAttributes',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CPSuri' => 
  array (
    'type' => 'class',
    'classname' => 'CPSuri',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CPSuri',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CRLDistributionPoints' => 
  array (
    'type' => 'class',
    'classname' => 'CRLDistributionPoints',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CRLDistributionPoints',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CRLNumber' => 
  array (
    'type' => 'class',
    'classname' => 'CRLNumber',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CRLNumber',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CRLReason' => 
  array (
    'type' => 'class',
    'classname' => 'CRLReason',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CRLReason',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertPolicyId' => 
  array (
    'type' => 'class',
    'classname' => 'CertPolicyId',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertPolicyId',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Certificate' => 
  array (
    'type' => 'class',
    'classname' => 'Certificate',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Certificate',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificateIssuer' => 
  array (
    'type' => 'class',
    'classname' => 'CertificateIssuer',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificateIssuer',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificateList' => 
  array (
    'type' => 'class',
    'classname' => 'CertificateList',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificateList',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificatePolicies' => 
  array (
    'type' => 'class',
    'classname' => 'CertificatePolicies',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificatePolicies',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificateSerialNumber' => 
  array (
    'type' => 'class',
    'classname' => 'CertificateSerialNumber',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificateSerialNumber',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificationRequest' => 
  array (
    'type' => 'class',
    'classname' => 'CertificationRequest',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificationRequest',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CertificationRequestInfo' => 
  array (
    'type' => 'class',
    'classname' => 'CertificationRequestInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CertificationRequestInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Characteristic_two' => 
  array (
    'type' => 'class',
    'classname' => 'Characteristic_two',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Characteristic_two',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\CountryName' => 
  array (
    'type' => 'class',
    'classname' => 'CountryName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\CountryName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Curve' => 
  array (
    'type' => 'class',
    'classname' => 'Curve',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Curve',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DHParameter' => 
  array (
    'type' => 'class',
    'classname' => 'DHParameter',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DHParameter',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DSAParams' => 
  array (
    'type' => 'class',
    'classname' => 'DSAParams',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DSAParams',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DSAPrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'DSAPrivateKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DSAPrivateKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DSAPublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'DSAPublicKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DSAPublicKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DigestInfo' => 
  array (
    'type' => 'class',
    'classname' => 'DigestInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DigestInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DirectoryString' => 
  array (
    'type' => 'class',
    'classname' => 'DirectoryString',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DirectoryString',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DisplayText' => 
  array (
    'type' => 'class',
    'classname' => 'DisplayText',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DisplayText',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DistributionPoint' => 
  array (
    'type' => 'class',
    'classname' => 'DistributionPoint',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DistributionPoint',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DistributionPointName' => 
  array (
    'type' => 'class',
    'classname' => 'DistributionPointName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DistributionPointName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\DssSigValue' => 
  array (
    'type' => 'class',
    'classname' => 'DssSigValue',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\DssSigValue',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ECParameters' => 
  array (
    'type' => 'class',
    'classname' => 'ECParameters',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ECParameters',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ECPoint' => 
  array (
    'type' => 'class',
    'classname' => 'ECPoint',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ECPoint',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ECPrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'ECPrivateKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ECPrivateKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\EDIPartyName' => 
  array (
    'type' => 'class',
    'classname' => 'EDIPartyName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\EDIPartyName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\EcdsaSigValue' => 
  array (
    'type' => 'class',
    'classname' => 'EcdsaSigValue',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\EcdsaSigValue',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\EncryptedData' => 
  array (
    'type' => 'class',
    'classname' => 'EncryptedData',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\EncryptedData',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\EncryptedPrivateKeyInfo' => 
  array (
    'type' => 'class',
    'classname' => 'EncryptedPrivateKeyInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\EncryptedPrivateKeyInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ExtKeyUsageSyntax' => 
  array (
    'type' => 'class',
    'classname' => 'ExtKeyUsageSyntax',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ExtKeyUsageSyntax',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Extension' => 
  array (
    'type' => 'class',
    'classname' => 'Extension',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Extension',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ExtensionAttribute' => 
  array (
    'type' => 'class',
    'classname' => 'ExtensionAttribute',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ExtensionAttribute',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ExtensionAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'ExtensionAttributes',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ExtensionAttributes',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Extensions' => 
  array (
    'type' => 'class',
    'classname' => 'Extensions',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Extensions',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\FieldElement' => 
  array (
    'type' => 'class',
    'classname' => 'FieldElement',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\FieldElement',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\FieldID' => 
  array (
    'type' => 'class',
    'classname' => 'FieldID',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\FieldID',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\GeneralName' => 
  array (
    'type' => 'class',
    'classname' => 'GeneralName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\GeneralName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\GeneralNames' => 
  array (
    'type' => 'class',
    'classname' => 'GeneralNames',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\GeneralNames',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\GeneralSubtree' => 
  array (
    'type' => 'class',
    'classname' => 'GeneralSubtree',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\GeneralSubtree',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\GeneralSubtrees' => 
  array (
    'type' => 'class',
    'classname' => 'GeneralSubtrees',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\GeneralSubtrees',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\HashAlgorithm' => 
  array (
    'type' => 'class',
    'classname' => 'HashAlgorithm',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\HashAlgorithm',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\HoldInstructionCode' => 
  array (
    'type' => 'class',
    'classname' => 'HoldInstructionCode',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\HoldInstructionCode',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\InvalidityDate' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidityDate',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\InvalidityDate',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\IssuerAltName' => 
  array (
    'type' => 'class',
    'classname' => 'IssuerAltName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\IssuerAltName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\IssuingDistributionPoint' => 
  array (
    'type' => 'class',
    'classname' => 'IssuingDistributionPoint',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\IssuingDistributionPoint',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\KeyIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'KeyIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\KeyIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\KeyPurposeId' => 
  array (
    'type' => 'class',
    'classname' => 'KeyPurposeId',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\KeyPurposeId',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\KeyUsage' => 
  array (
    'type' => 'class',
    'classname' => 'KeyUsage',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\KeyUsage',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\MaskGenAlgorithm' => 
  array (
    'type' => 'class',
    'classname' => 'MaskGenAlgorithm',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\MaskGenAlgorithm',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Name' => 
  array (
    'type' => 'class',
    'classname' => 'Name',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Name',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\NameConstraints' => 
  array (
    'type' => 'class',
    'classname' => 'NameConstraints',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\NameConstraints',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\NetworkAddress' => 
  array (
    'type' => 'class',
    'classname' => 'NetworkAddress',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\NetworkAddress',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\NoticeReference' => 
  array (
    'type' => 'class',
    'classname' => 'NoticeReference',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\NoticeReference',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\NumericUserIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'NumericUserIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\NumericUserIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ORAddress' => 
  array (
    'type' => 'class',
    'classname' => 'ORAddress',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ORAddress',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\OneAsymmetricKey' => 
  array (
    'type' => 'class',
    'classname' => 'OneAsymmetricKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\OneAsymmetricKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\OrganizationName' => 
  array (
    'type' => 'class',
    'classname' => 'OrganizationName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\OrganizationName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\OrganizationalUnitNames' => 
  array (
    'type' => 'class',
    'classname' => 'OrganizationalUnitNames',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\OrganizationalUnitNames',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\OtherPrimeInfo' => 
  array (
    'type' => 'class',
    'classname' => 'OtherPrimeInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\OtherPrimeInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\OtherPrimeInfos' => 
  array (
    'type' => 'class',
    'classname' => 'OtherPrimeInfos',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\OtherPrimeInfos',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PBEParameter' => 
  array (
    'type' => 'class',
    'classname' => 'PBEParameter',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PBEParameter',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PBES2params' => 
  array (
    'type' => 'class',
    'classname' => 'PBES2params',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PBES2params',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PBKDF2params' => 
  array (
    'type' => 'class',
    'classname' => 'PBKDF2params',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PBKDF2params',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PBMAC1params' => 
  array (
    'type' => 'class',
    'classname' => 'PBMAC1params',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PBMAC1params',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PKCS9String' => 
  array (
    'type' => 'class',
    'classname' => 'PKCS9String',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PKCS9String',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Pentanomial' => 
  array (
    'type' => 'class',
    'classname' => 'Pentanomial',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Pentanomial',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PersonalName' => 
  array (
    'type' => 'class',
    'classname' => 'PersonalName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PersonalName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PolicyInformation' => 
  array (
    'type' => 'class',
    'classname' => 'PolicyInformation',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PolicyInformation',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PolicyMappings' => 
  array (
    'type' => 'class',
    'classname' => 'PolicyMappings',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PolicyMappings',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PolicyQualifierId' => 
  array (
    'type' => 'class',
    'classname' => 'PolicyQualifierId',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PolicyQualifierId',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PolicyQualifierInfo' => 
  array (
    'type' => 'class',
    'classname' => 'PolicyQualifierInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PolicyQualifierInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PostalAddress' => 
  array (
    'type' => 'class',
    'classname' => 'PostalAddress',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PostalAddress',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Prime_p' => 
  array (
    'type' => 'class',
    'classname' => 'Prime_p',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Prime_p',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PrivateDomainName' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateDomainName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PrivateDomainName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PrivateKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PrivateKeyInfo' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKeyInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PrivateKeyInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PrivateKeyUsagePeriod' => 
  array (
    'type' => 'class',
    'classname' => 'PrivateKeyUsagePeriod',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PrivateKeyUsagePeriod',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PublicKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PublicKeyAndChallenge' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKeyAndChallenge',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PublicKeyAndChallenge',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\PublicKeyInfo' => 
  array (
    'type' => 'class',
    'classname' => 'PublicKeyInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\PublicKeyInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RC2CBCParameter' => 
  array (
    'type' => 'class',
    'classname' => 'RC2CBCParameter',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RC2CBCParameter',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RDNSequence' => 
  array (
    'type' => 'class',
    'classname' => 'RDNSequence',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RDNSequence',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RSAPrivateKey' => 
  array (
    'type' => 'class',
    'classname' => 'RSAPrivateKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RSAPrivateKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RSAPublicKey' => 
  array (
    'type' => 'class',
    'classname' => 'RSAPublicKey',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RSAPublicKey',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RSASSA_PSS_params' => 
  array (
    'type' => 'class',
    'classname' => 'RSASSA_PSS_params',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RSASSA_PSS_params',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\ReasonFlags' => 
  array (
    'type' => 'class',
    'classname' => 'ReasonFlags',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\ReasonFlags',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RelativeDistinguishedName' => 
  array (
    'type' => 'class',
    'classname' => 'RelativeDistinguishedName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RelativeDistinguishedName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\RevokedCertificate' => 
  array (
    'type' => 'class',
    'classname' => 'RevokedCertificate',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\RevokedCertificate',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SignedPublicKeyAndChallenge' => 
  array (
    'type' => 'class',
    'classname' => 'SignedPublicKeyAndChallenge',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SignedPublicKeyAndChallenge',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SpecifiedECDomain' => 
  array (
    'type' => 'class',
    'classname' => 'SpecifiedECDomain',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SpecifiedECDomain',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SubjectAltName' => 
  array (
    'type' => 'class',
    'classname' => 'SubjectAltName',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SubjectAltName',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SubjectDirectoryAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'SubjectDirectoryAttributes',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SubjectDirectoryAttributes',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SubjectInfoAccessSyntax' => 
  array (
    'type' => 'class',
    'classname' => 'SubjectInfoAccessSyntax',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SubjectInfoAccessSyntax',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\SubjectPublicKeyInfo' => 
  array (
    'type' => 'class',
    'classname' => 'SubjectPublicKeyInfo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\SubjectPublicKeyInfo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\TBSCertList' => 
  array (
    'type' => 'class',
    'classname' => 'TBSCertList',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\TBSCertList',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\TBSCertificate' => 
  array (
    'type' => 'class',
    'classname' => 'TBSCertificate',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\TBSCertificate',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\TerminalIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'TerminalIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\TerminalIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Time' => 
  array (
    'type' => 'class',
    'classname' => 'Time',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Time',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Trinomial' => 
  array (
    'type' => 'class',
    'classname' => 'Trinomial',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Trinomial',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\UniqueIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'UniqueIdentifier',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\UniqueIdentifier',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\UserNotice' => 
  array (
    'type' => 'class',
    'classname' => 'UserNotice',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\UserNotice',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\Validity' => 
  array (
    'type' => 'class',
    'classname' => 'Validity',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\Validity',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\netscape_ca_policy_url' => 
  array (
    'type' => 'class',
    'classname' => 'netscape_ca_policy_url',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\netscape_ca_policy_url',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\netscape_cert_type' => 
  array (
    'type' => 'class',
    'classname' => 'netscape_cert_type',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\netscape_cert_type',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\ASN1\\Maps\\netscape_comment' => 
  array (
    'type' => 'class',
    'classname' => 'netscape_comment',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\File\\ASN1\\Maps',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\ASN1\\Maps\\netscape_comment',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\File\\X509' => 
  array (
    'type' => 'class',
    'classname' => 'X509',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\File',
    'extends' => 'AmeliaVendor\\phpseclib3\\File\\X509',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger' => 
  array (
    'type' => 'class',
    'classname' => 'BigInteger',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Math',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Base' => 
  array (
    'type' => 'class',
    'classname' => 'Base',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Base',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\BuiltIn' => 
  array (
    'type' => 'class',
    'classname' => 'BuiltIn',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\BuiltIn',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\DefaultEngine' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultEngine',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\DefaultEngine',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\OpenSSL' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSL',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\OpenSSL',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions\\Barrett' => 
  array (
    'type' => 'class',
    'classname' => 'Barrett',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions\\Barrett',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions\\EvalBarrett' => 
  array (
    'type' => 'class',
    'classname' => 'EvalBarrett',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\BCMath\\Reductions\\EvalBarrett',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\Engine' => 
  array (
    'type' => 'class',
    'classname' => 'Engine',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\Engine',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\GMP\\DefaultEngine' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultEngine',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\GMP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\GMP\\DefaultEngine',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\OpenSSL' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSL',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\OpenSSL',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Base' => 
  array (
    'type' => 'class',
    'classname' => 'Base',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Base',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\DefaultEngine' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultEngine',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\DefaultEngine',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Montgomery' => 
  array (
    'type' => 'class',
    'classname' => 'Montgomery',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Montgomery',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\OpenSSL' => 
  array (
    'type' => 'class',
    'classname' => 'OpenSSL',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\OpenSSL',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Barrett' => 
  array (
    'type' => 'class',
    'classname' => 'Barrett',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Barrett',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Classic' => 
  array (
    'type' => 'class',
    'classname' => 'Classic',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Classic',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\EvalBarrett' => 
  array (
    'type' => 'class',
    'classname' => 'EvalBarrett',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\EvalBarrett',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Montgomery' => 
  array (
    'type' => 'class',
    'classname' => 'Montgomery',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\Montgomery',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\MontgomeryMult' => 
  array (
    'type' => 'class',
    'classname' => 'MontgomeryMult',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\MontgomeryMult',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\PowerOfTwo' => 
  array (
    'type' => 'class',
    'classname' => 'PowerOfTwo',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP\\Reductions\\PowerOfTwo',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP32' => 
  array (
    'type' => 'class',
    'classname' => 'PHP32',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP32',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BigInteger\\Engines\\PHP64' => 
  array (
    'type' => 'class',
    'classname' => 'PHP64',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Math\\BigInteger\\Engines',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BigInteger\\Engines\\PHP64',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\BinaryField\\Integer' => 
  array (
    'type' => 'class',
    'classname' => 'Integer',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Math\\BinaryField',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\BinaryField\\Integer',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Math\\Common\\FiniteField\\Integer' => 
  array (
    'type' => 'class',
    'classname' => 'Integer',
    'isabstract' => true,
    'namespace' => 'phpseclib3\\Math\\Common\\FiniteField',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\Common\\FiniteField\\Integer',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'phpseclib3\\Math\\PrimeField\\Integer' => 
  array (
    'type' => 'class',
    'classname' => 'Integer',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Math\\PrimeField',
    'extends' => 'AmeliaVendor\\phpseclib3\\Math\\PrimeField\\Integer',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Net\\SFTP\\Stream' => 
  array (
    'type' => 'class',
    'classname' => 'Stream',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Net\\SFTP',
    'extends' => 'AmeliaVendor\\phpseclib3\\Net\\SFTP\\Stream',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\Net\\SSH2' => 
  array (
    'type' => 'class',
    'classname' => 'SSH2',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\Net',
    'extends' => 'AmeliaVendor\\phpseclib3\\Net\\SSH2',
    'implements' => 
    array (
    ),
  ),
  'phpseclib3\\System\\SSH\\Agent\\Identity' => 
  array (
    'type' => 'class',
    'classname' => 'Identity',
    'isabstract' => false,
    'namespace' => 'phpseclib3\\System\\SSH\\Agent',
    'extends' => 'AmeliaVendor\\phpseclib3\\System\\SSH\\Agent\\Identity',
    'implements' => 
    array (
      0 => 'phpseclib3\\Crypt\\Common\\PrivateKey',
    ),
  ),
  'Psr\\Log\\AbstractLogger' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractLogger',
    'isabstract' => true,
    'namespace' => 'Psr\\Log',
    'extends' => 'AmeliaVendor\\Psr\\Log\\AbstractLogger',
    'implements' => 
    array (
      0 => 'Psr\\Log\\LoggerInterface',
    ),
  ),
  'Psr\\Log\\InvalidArgumentException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidArgumentException',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'AmeliaVendor\\Psr\\Log\\InvalidArgumentException',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\LogLevel' => 
  array (
    'type' => 'class',
    'classname' => 'LogLevel',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'AmeliaVendor\\Psr\\Log\\LogLevel',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\NullLogger' => 
  array (
    'type' => 'class',
    'classname' => 'NullLogger',
    'isabstract' => false,
    'namespace' => 'Psr\\Log',
    'extends' => 'AmeliaVendor\\Psr\\Log\\NullLogger',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\DummyTest' => 
  array (
    'type' => 'class',
    'classname' => 'DummyTest',
    'isabstract' => false,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'AmeliaVendor\\Psr\\Log\\Test\\DummyTest',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\LoggerInterfaceTest' => 
  array (
    'type' => 'class',
    'classname' => 'LoggerInterfaceTest',
    'isabstract' => true,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'AmeliaVendor\\Psr\\Log\\Test\\LoggerInterfaceTest',
    'implements' => 
    array (
    ),
  ),
  'Psr\\Log\\Test\\TestLogger' => 
  array (
    'type' => 'class',
    'classname' => 'TestLogger',
    'isabstract' => false,
    'namespace' => 'Psr\\Log\\Test',
    'extends' => 'AmeliaVendor\\Psr\\Log\\Test\\TestLogger',
    'implements' => 
    array (
    ),
  ),
  'Requests' => 
  array (
    'type' => 'class',
    'classname' => 'Requests',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'AmeliaVendor_Requests',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Auth\\Basic' => 
  array (
    'type' => 'class',
    'classname' => 'Basic',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Auth',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Auth\\Basic',
    'implements' => 
    array (
      0 => 'WpOrg\\Requests\\Auth',
    ),
  ),
  'WpOrg\\Requests\\Autoload' => 
  array (
    'type' => 'class',
    'classname' => 'Autoload',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Autoload',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Cookie\\Jar' => 
  array (
    'type' => 'class',
    'classname' => 'Jar',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Cookie',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Cookie\\Jar',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'IteratorAggregate',
    ),
  ),
  'WpOrg\\Requests\\Exception\\ArgumentCount' => 
  array (
    'type' => 'class',
    'classname' => 'ArgumentCount',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\ArgumentCount',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status304' => 
  array (
    'type' => 'class',
    'classname' => 'Status304',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status304',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status305' => 
  array (
    'type' => 'class',
    'classname' => 'Status305',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status305',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status306' => 
  array (
    'type' => 'class',
    'classname' => 'Status306',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status306',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status400' => 
  array (
    'type' => 'class',
    'classname' => 'Status400',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status400',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status401' => 
  array (
    'type' => 'class',
    'classname' => 'Status401',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status401',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status402' => 
  array (
    'type' => 'class',
    'classname' => 'Status402',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status402',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status403' => 
  array (
    'type' => 'class',
    'classname' => 'Status403',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status403',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status404' => 
  array (
    'type' => 'class',
    'classname' => 'Status404',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status404',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status405' => 
  array (
    'type' => 'class',
    'classname' => 'Status405',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status405',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status406' => 
  array (
    'type' => 'class',
    'classname' => 'Status406',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status406',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status407' => 
  array (
    'type' => 'class',
    'classname' => 'Status407',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status407',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status408' => 
  array (
    'type' => 'class',
    'classname' => 'Status408',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status408',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status409' => 
  array (
    'type' => 'class',
    'classname' => 'Status409',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status409',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status410' => 
  array (
    'type' => 'class',
    'classname' => 'Status410',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status410',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status411' => 
  array (
    'type' => 'class',
    'classname' => 'Status411',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status411',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status412' => 
  array (
    'type' => 'class',
    'classname' => 'Status412',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status412',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status413' => 
  array (
    'type' => 'class',
    'classname' => 'Status413',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status413',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status414' => 
  array (
    'type' => 'class',
    'classname' => 'Status414',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status414',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status415' => 
  array (
    'type' => 'class',
    'classname' => 'Status415',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status415',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status416' => 
  array (
    'type' => 'class',
    'classname' => 'Status416',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status416',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status417' => 
  array (
    'type' => 'class',
    'classname' => 'Status417',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status417',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status418' => 
  array (
    'type' => 'class',
    'classname' => 'Status418',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status418',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status428' => 
  array (
    'type' => 'class',
    'classname' => 'Status428',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status428',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status429' => 
  array (
    'type' => 'class',
    'classname' => 'Status429',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status429',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status431' => 
  array (
    'type' => 'class',
    'classname' => 'Status431',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status431',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status500' => 
  array (
    'type' => 'class',
    'classname' => 'Status500',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status500',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status501' => 
  array (
    'type' => 'class',
    'classname' => 'Status501',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status501',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status502' => 
  array (
    'type' => 'class',
    'classname' => 'Status502',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status502',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status503' => 
  array (
    'type' => 'class',
    'classname' => 'Status503',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status503',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status504' => 
  array (
    'type' => 'class',
    'classname' => 'Status504',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status504',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status505' => 
  array (
    'type' => 'class',
    'classname' => 'Status505',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status505',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\Status511' => 
  array (
    'type' => 'class',
    'classname' => 'Status511',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\Status511',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Http\\StatusUnknown' => 
  array (
    'type' => 'class',
    'classname' => 'StatusUnknown',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Http',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Http\\StatusUnknown',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\InvalidArgument' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidArgument',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\InvalidArgument',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Exception\\Transport\\Curl' => 
  array (
    'type' => 'class',
    'classname' => 'Curl',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Exception\\Transport',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Exception\\Transport\\Curl',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Hooks' => 
  array (
    'type' => 'class',
    'classname' => 'Hooks',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Hooks',
    'implements' => 
    array (
      0 => 'WpOrg\\Requests\\HookManager',
    ),
  ),
  'WpOrg\\Requests\\IdnaEncoder' => 
  array (
    'type' => 'class',
    'classname' => 'IdnaEncoder',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\IdnaEncoder',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Ipv6' => 
  array (
    'type' => 'class',
    'classname' => 'Ipv6',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Ipv6',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Iri' => 
  array (
    'type' => 'class',
    'classname' => 'Iri',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Iri',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Port' => 
  array (
    'type' => 'class',
    'classname' => 'Port',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Port',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Proxy\\Http' => 
  array (
    'type' => 'class',
    'classname' => 'Http',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Proxy',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Proxy\\Http',
    'implements' => 
    array (
      0 => 'WpOrg\\Requests\\Proxy',
    ),
  ),
  'WpOrg\\Requests\\Requests' => 
  array (
    'type' => 'class',
    'classname' => 'Requests',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Requests',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Response\\Headers' => 
  array (
    'type' => 'class',
    'classname' => 'Headers',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Response',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Response\\Headers',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Session' => 
  array (
    'type' => 'class',
    'classname' => 'Session',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Session',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Ssl' => 
  array (
    'type' => 'class',
    'classname' => 'Ssl',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Ssl',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Transport\\Curl' => 
  array (
    'type' => 'class',
    'classname' => 'Curl',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Transport',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Transport\\Curl',
    'implements' => 
    array (
      0 => 'WpOrg\\Requests\\Transport',
    ),
  ),
  'WpOrg\\Requests\\Transport\\Fsockopen' => 
  array (
    'type' => 'class',
    'classname' => 'Fsockopen',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Transport',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Transport\\Fsockopen',
    'implements' => 
    array (
      0 => 'WpOrg\\Requests\\Transport',
    ),
  ),
  'WpOrg\\Requests\\Utility\\CaseInsensitiveDictionary' => 
  array (
    'type' => 'class',
    'classname' => 'CaseInsensitiveDictionary',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Utility',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Utility\\CaseInsensitiveDictionary',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'IteratorAggregate',
    ),
  ),
  'WpOrg\\Requests\\Utility\\FilteredIterator' => 
  array (
    'type' => 'class',
    'classname' => 'FilteredIterator',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Utility',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Utility\\FilteredIterator',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Utility\\InputValidator' => 
  array (
    'type' => 'class',
    'classname' => 'InputValidator',
    'isabstract' => false,
    'namespace' => 'WpOrg\\Requests\\Utility',
    'extends' => 'AmeliaVendor\\WpOrg\\Requests\\Utility\\InputValidator',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\CSSList\\AtRuleBlockList' => 
  array (
    'type' => 'class',
    'classname' => 'AtRuleBlockList',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\CSSList',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\CSSList\\AtRuleBlockList',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
    ),
  ),
  'Sabberworm\\CSS\\CSSList\\CSSBlockList' => 
  array (
    'type' => 'class',
    'classname' => 'CSSBlockList',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\CSSList',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\CSSList\\CSSBlockList',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\CSSList\\CSSList' => 
  array (
    'type' => 'class',
    'classname' => 'CSSList',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\CSSList',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\CSSList\\CSSList',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Comment\\Commentable',
      1 => 'Sabberworm\\CSS\\CSSElement',
      2 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\CSSList\\Document' => 
  array (
    'type' => 'class',
    'classname' => 'Document',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\CSSList',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\CSSList\\Document',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\CSSList\\KeyFrame' => 
  array (
    'type' => 'class',
    'classname' => 'KeyFrame',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\CSSList',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\CSSList\\KeyFrame',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
    ),
  ),
  'Sabberworm\\CSS\\Comment\\Comment' => 
  array (
    'type' => 'class',
    'classname' => 'Comment',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Comment',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Comment\\Comment',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Position\\Positionable',
      1 => 'Sabberworm\\CSS\\Renderable',
    ),
  ),
  'Sabberworm\\CSS\\OutputFormat' => 
  array (
    'type' => 'class',
    'classname' => 'OutputFormat',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\OutputFormat',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\OutputFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'OutputFormatter',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\OutputFormatter',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parser' => 
  array (
    'type' => 'class',
    'classname' => 'Parser',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parser',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\Anchor' => 
  array (
    'type' => 'class',
    'classname' => 'Anchor',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\Anchor',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\OutputException' => 
  array (
    'type' => 'class',
    'classname' => 'OutputException',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\OutputException',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\ParserState' => 
  array (
    'type' => 'class',
    'classname' => 'ParserState',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\ParserState',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\SourceException' => 
  array (
    'type' => 'class',
    'classname' => 'SourceException',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\SourceException',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\UnexpectedEOFException' => 
  array (
    'type' => 'class',
    'classname' => 'UnexpectedEOFException',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\UnexpectedEOFException',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Parsing\\UnexpectedTokenException' => 
  array (
    'type' => 'class',
    'classname' => 'UnexpectedTokenException',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Parsing',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Parsing\\UnexpectedTokenException',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Property\\CSSNamespace' => 
  array (
    'type' => 'class',
    'classname' => 'CSSNamespace',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\CSSNamespace',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
      1 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Property\\Charset' => 
  array (
    'type' => 'class',
    'classname' => 'Charset',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\Charset',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
      1 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Property\\Import' => 
  array (
    'type' => 'class',
    'classname' => 'Import',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\Import',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
      1 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Property\\KeyframeSelector' => 
  array (
    'type' => 'class',
    'classname' => 'KeyframeSelector',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\KeyframeSelector',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Property\\Selector' => 
  array (
    'type' => 'class',
    'classname' => 'Selector',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\Selector',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Rule\\Rule' => 
  array (
    'type' => 'class',
    'classname' => 'Rule',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Rule',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Rule\\Rule',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Comment\\Commentable',
      1 => 'Sabberworm\\CSS\\CSSElement',
      2 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\RuleSet\\AtRuleSet' => 
  array (
    'type' => 'class',
    'classname' => 'AtRuleSet',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\RuleSet',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\RuleSet\\AtRuleSet',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\Property\\AtRule',
    ),
  ),
  'Sabberworm\\CSS\\RuleSet\\DeclarationBlock' => 
  array (
    'type' => 'class',
    'classname' => 'DeclarationBlock',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\RuleSet',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\RuleSet\\DeclarationBlock',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\RuleSet\\RuleSet' => 
  array (
    'type' => 'class',
    'classname' => 'RuleSet',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\RuleSet',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\RuleSet\\RuleSet',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\CSSElement',
      1 => 'Sabberworm\\CSS\\Comment\\Commentable',
      2 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Settings',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\CSSFunction' => 
  array (
    'type' => 'class',
    'classname' => 'CSSFunction',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\CSSFunction',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\CSSString' => 
  array (
    'type' => 'class',
    'classname' => 'CSSString',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\CSSString',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\CalcFunction' => 
  array (
    'type' => 'class',
    'classname' => 'CalcFunction',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\CalcFunction',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\CalcRuleValueList' => 
  array (
    'type' => 'class',
    'classname' => 'CalcRuleValueList',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\CalcRuleValueList',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\Color' => 
  array (
    'type' => 'class',
    'classname' => 'Color',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\Color',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\LineName' => 
  array (
    'type' => 'class',
    'classname' => 'LineName',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\LineName',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\PrimitiveValue' => 
  array (
    'type' => 'class',
    'classname' => 'PrimitiveValue',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\PrimitiveValue',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\RuleValueList' => 
  array (
    'type' => 'class',
    'classname' => 'RuleValueList',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\RuleValueList',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\Size' => 
  array (
    'type' => 'class',
    'classname' => 'Size',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\Size',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\URL' => 
  array (
    'type' => 'class',
    'classname' => 'URL',
    'isabstract' => false,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\URL',
    'implements' => 
    array (
    ),
  ),
  'Sabberworm\\CSS\\Value\\Value' => 
  array (
    'type' => 'class',
    'classname' => 'Value',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\Value',
    'implements' => 
    array (
      0 => 'Sabberworm\\CSS\\CSSElement',
      1 => 'Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Value\\ValueList' => 
  array (
    'type' => 'class',
    'classname' => 'ValueList',
    'isabstract' => true,
    'namespace' => 'Sabberworm\\CSS\\Value',
    'extends' => 'AmeliaVendor\\Sabberworm\\CSS\\Value\\ValueList',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Uri\\InvalidUriException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidUriException',
    'isabstract' => false,
    'namespace' => 'Sabre\\Uri',
    'extends' => 'AmeliaVendor\\Sabre\\Uri\\InvalidUriException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Uri\\Version' => 
  array (
    'type' => 'class',
    'classname' => 'Version',
    'isabstract' => false,
    'namespace' => 'Sabre\\Uri',
    'extends' => 'AmeliaVendor\\Sabre\\Uri\\Version',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\BirthdayCalendarGenerator' => 
  array (
    'type' => 'class',
    'classname' => 'BirthdayCalendarGenerator',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\BirthdayCalendarGenerator',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Cli' => 
  array (
    'type' => 'class',
    'classname' => 'Cli',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Cli',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\Available' => 
  array (
    'type' => 'class',
    'classname' => 'Available',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\Available',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VAlarm' => 
  array (
    'type' => 'class',
    'classname' => 'VAlarm',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VAlarm',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VAvailability' => 
  array (
    'type' => 'class',
    'classname' => 'VAvailability',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VAvailability',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VCalendar' => 
  array (
    'type' => 'class',
    'classname' => 'VCalendar',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VCalendar',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VCard' => 
  array (
    'type' => 'class',
    'classname' => 'VCard',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VCard',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VEvent' => 
  array (
    'type' => 'class',
    'classname' => 'VEvent',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VEvent',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VFreeBusy' => 
  array (
    'type' => 'class',
    'classname' => 'VFreeBusy',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VFreeBusy',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VJournal' => 
  array (
    'type' => 'class',
    'classname' => 'VJournal',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VJournal',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VTimeZone' => 
  array (
    'type' => 'class',
    'classname' => 'VTimeZone',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VTimeZone',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Component\\VTodo' => 
  array (
    'type' => 'class',
    'classname' => 'VTodo',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Component',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Component\\VTodo',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\DateTimeParser' => 
  array (
    'type' => 'class',
    'classname' => 'DateTimeParser',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\DateTimeParser',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Document' => 
  array (
    'type' => 'class',
    'classname' => 'Document',
    'isabstract' => true,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Document',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ElementList' => 
  array (
    'type' => 'class',
    'classname' => 'ElementList',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ElementList',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\EofException' => 
  array (
    'type' => 'class',
    'classname' => 'EofException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\EofException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\FreeBusyData' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyData',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\FreeBusyData',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\FreeBusyGenerator' => 
  array (
    'type' => 'class',
    'classname' => 'FreeBusyGenerator',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\FreeBusyGenerator',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ITip\\Broker' => 
  array (
    'type' => 'class',
    'classname' => 'Broker',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\ITip',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ITip\\Broker',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ITip\\ITipException' => 
  array (
    'type' => 'class',
    'classname' => 'ITipException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\ITip',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ITip\\ITipException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ITip\\Message' => 
  array (
    'type' => 'class',
    'classname' => 'Message',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\ITip',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ITip\\Message',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ITip\\SameOrganizerForAllComponentsException' => 
  array (
    'type' => 'class',
    'classname' => 'SameOrganizerForAllComponentsException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\ITip',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ITip\\SameOrganizerForAllComponentsException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\InvalidDataException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidDataException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\InvalidDataException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Node' => 
  array (
    'type' => 'class',
    'classname' => 'Node',
    'isabstract' => true,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Node',
    'implements' => 
    array (
      0 => 'IteratorAggregate',
      1 => 'ArrayAccess',
      2 => 'Countable',
      3 => 'JsonSerializable',
      4 => 'Sabre\\Xml\\XmlSerializable',
    ),
  ),
  'Sabre\\VObject\\Parameter' => 
  array (
    'type' => 'class',
    'classname' => 'Parameter',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parameter',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\ParseException' => 
  array (
    'type' => 'class',
    'classname' => 'ParseException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\ParseException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Parser\\Json' => 
  array (
    'type' => 'class',
    'classname' => 'Json',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Parser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parser\\Json',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Parser\\MimeDir' => 
  array (
    'type' => 'class',
    'classname' => 'MimeDir',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Parser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parser\\MimeDir',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Parser\\Parser' => 
  array (
    'type' => 'class',
    'classname' => 'Parser',
    'isabstract' => true,
    'namespace' => 'Sabre\\VObject\\Parser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parser\\Parser',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Parser\\XML' => 
  array (
    'type' => 'class',
    'classname' => 'XML',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Parser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parser\\XML',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Parser\\XML\\Element\\KeyValue' => 
  array (
    'type' => 'class',
    'classname' => 'KeyValue',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Parser\\XML\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Parser\\XML\\Element\\KeyValue',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Binary' => 
  array (
    'type' => 'class',
    'classname' => 'Binary',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Binary',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Boolean' => 
  array (
    'type' => 'class',
    'classname' => 'Boolean',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Boolean',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\FlatText' => 
  array (
    'type' => 'class',
    'classname' => 'FlatText',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\FlatText',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\FloatValue' => 
  array (
    'type' => 'class',
    'classname' => 'FloatValue',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\FloatValue',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\CalAddress' => 
  array (
    'type' => 'class',
    'classname' => 'CalAddress',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\CalAddress',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\Date' => 
  array (
    'type' => 'class',
    'classname' => 'Date',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\Date',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\DateTime' => 
  array (
    'type' => 'class',
    'classname' => 'DateTime',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\DateTime',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\Duration' => 
  array (
    'type' => 'class',
    'classname' => 'Duration',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\Duration',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\Period' => 
  array (
    'type' => 'class',
    'classname' => 'Period',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\Period',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\ICalendar\\Recur' => 
  array (
    'type' => 'class',
    'classname' => 'Recur',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\ICalendar',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\ICalendar\\Recur',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\IntegerValue' => 
  array (
    'type' => 'class',
    'classname' => 'IntegerValue',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\IntegerValue',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Text',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Time' => 
  array (
    'type' => 'class',
    'classname' => 'Time',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Time',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Unknown' => 
  array (
    'type' => 'class',
    'classname' => 'Unknown',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Unknown',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\Uri' => 
  array (
    'type' => 'class',
    'classname' => 'Uri',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\Uri',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\UtcOffset' => 
  array (
    'type' => 'class',
    'classname' => 'UtcOffset',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\UtcOffset',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\Date' => 
  array (
    'type' => 'class',
    'classname' => 'Date',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\Date',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\DateAndOrTime' => 
  array (
    'type' => 'class',
    'classname' => 'DateAndOrTime',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\DateAndOrTime',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\DateTime' => 
  array (
    'type' => 'class',
    'classname' => 'DateTime',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\DateTime',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\LanguageTag' => 
  array (
    'type' => 'class',
    'classname' => 'LanguageTag',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\LanguageTag',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\PhoneNumber' => 
  array (
    'type' => 'class',
    'classname' => 'PhoneNumber',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\PhoneNumber',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Property\\VCard\\TimeStamp' => 
  array (
    'type' => 'class',
    'classname' => 'TimeStamp',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Property\\VCard',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Property\\VCard\\TimeStamp',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Reader' => 
  array (
    'type' => 'class',
    'classname' => 'Reader',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Reader',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Recur\\EventIterator' => 
  array (
    'type' => 'class',
    'classname' => 'EventIterator',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Recur',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Recur\\EventIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'Sabre\\VObject\\Recur\\MaxInstancesExceededException' => 
  array (
    'type' => 'class',
    'classname' => 'MaxInstancesExceededException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Recur',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Recur\\MaxInstancesExceededException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Recur\\NoInstancesException' => 
  array (
    'type' => 'class',
    'classname' => 'NoInstancesException',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Recur',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Recur\\NoInstancesException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Recur\\RDateIterator' => 
  array (
    'type' => 'class',
    'classname' => 'RDateIterator',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Recur',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Recur\\RDateIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'Sabre\\VObject\\Recur\\RRuleIterator' => 
  array (
    'type' => 'class',
    'classname' => 'RRuleIterator',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Recur',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Recur\\RRuleIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'Sabre\\VObject\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Settings',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Splitter\\ICalendar' => 
  array (
    'type' => 'class',
    'classname' => 'ICalendar',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Splitter',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Splitter\\ICalendar',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\Splitter\\SplitterInterface',
    ),
  ),
  'Sabre\\VObject\\Splitter\\VCard' => 
  array (
    'type' => 'class',
    'classname' => 'VCard',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\Splitter',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Splitter\\VCard',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\Splitter\\SplitterInterface',
    ),
  ),
  'Sabre\\VObject\\StringUtil' => 
  array (
    'type' => 'class',
    'classname' => 'StringUtil',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\StringUtil',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\TimeZoneUtil' => 
  array (
    'type' => 'class',
    'classname' => 'TimeZoneUtil',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimeZoneUtil',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\FindFromOffset' => 
  array (
    'type' => 'class',
    'classname' => 'FindFromOffset',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\FindFromOffset',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\TimezoneGuesser\\TimezoneFinder',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\FindFromTimezoneIdentifier' => 
  array (
    'type' => 'class',
    'classname' => 'FindFromTimezoneIdentifier',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\FindFromTimezoneIdentifier',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\TimezoneGuesser\\TimezoneFinder',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\FindFromTimezoneMap' => 
  array (
    'type' => 'class',
    'classname' => 'FindFromTimezoneMap',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\FindFromTimezoneMap',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\TimezoneGuesser\\TimezoneFinder',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\GuessFromLicEntry' => 
  array (
    'type' => 'class',
    'classname' => 'GuessFromLicEntry',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\GuessFromLicEntry',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\TimezoneGuesser\\TimezoneGuesser',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\GuessFromMsTzId' => 
  array (
    'type' => 'class',
    'classname' => 'GuessFromMsTzId',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\GuessFromMsTzId',
    'implements' => 
    array (
      0 => 'Sabre\\VObject\\TimezoneGuesser\\TimezoneGuesser',
    ),
  ),
  'Sabre\\VObject\\UUIDUtil' => 
  array (
    'type' => 'class',
    'classname' => 'UUIDUtil',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\UUIDUtil',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\VCardConverter' => 
  array (
    'type' => 'class',
    'classname' => 'VCardConverter',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\VCardConverter',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Version' => 
  array (
    'type' => 'class',
    'classname' => 'Version',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Version',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\VObject\\Writer' => 
  array (
    'type' => 'class',
    'classname' => 'Writer',
    'isabstract' => false,
    'namespace' => 'Sabre\\VObject',
    'extends' => 'AmeliaVendor\\Sabre\\VObject\\Writer',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\Element\\Base' => 
  array (
    'type' => 'class',
    'classname' => 'Base',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\Base',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\Element',
    ),
  ),
  'Sabre\\Xml\\Element\\Cdata' => 
  array (
    'type' => 'class',
    'classname' => 'Cdata',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\Cdata',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\XmlSerializable',
    ),
  ),
  'Sabre\\Xml\\Element\\Elements' => 
  array (
    'type' => 'class',
    'classname' => 'Elements',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\Elements',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\Element',
    ),
  ),
  'Sabre\\Xml\\Element\\KeyValue' => 
  array (
    'type' => 'class',
    'classname' => 'KeyValue',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\KeyValue',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\Element',
    ),
  ),
  'Sabre\\Xml\\Element\\Uri' => 
  array (
    'type' => 'class',
    'classname' => 'Uri',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\Uri',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\Element',
    ),
  ),
  'Sabre\\Xml\\Element\\XmlFragment' => 
  array (
    'type' => 'class',
    'classname' => 'XmlFragment',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml\\Element',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Element\\XmlFragment',
    'implements' => 
    array (
      0 => 'Sabre\\Xml\\Element',
    ),
  ),
  'Sabre\\Xml\\LibXMLException' => 
  array (
    'type' => 'class',
    'classname' => 'LibXMLException',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\LibXMLException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\ParseException' => 
  array (
    'type' => 'class',
    'classname' => 'ParseException',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\ParseException',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\Reader' => 
  array (
    'type' => 'class',
    'classname' => 'Reader',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Reader',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\Service' => 
  array (
    'type' => 'class',
    'classname' => 'Service',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Service',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\Version' => 
  array (
    'type' => 'class',
    'classname' => 'Version',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Version',
    'implements' => 
    array (
    ),
  ),
  'Sabre\\Xml\\Writer' => 
  array (
    'type' => 'class',
    'classname' => 'Writer',
    'isabstract' => false,
    'namespace' => 'Sabre\\Xml',
    'extends' => 'AmeliaVendor\\Sabre\\Xml\\Writer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Account' => 
  array (
    'type' => 'class',
    'classname' => 'Account',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Account',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\AccountLink' => 
  array (
    'type' => 'class',
    'classname' => 'AccountLink',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\AccountLink',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\AccountSession' => 
  array (
    'type' => 'class',
    'classname' => 'AccountSession',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\AccountSession',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApiRequestor' => 
  array (
    'type' => 'class',
    'classname' => 'ApiRequestor',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApiRequestor',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApiResource' => 
  array (
    'type' => 'class',
    'classname' => 'ApiResource',
    'isabstract' => true,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApiResource',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApiResponse' => 
  array (
    'type' => 'class',
    'classname' => 'ApiResponse',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApiResponse',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApplePayDomain' => 
  array (
    'type' => 'class',
    'classname' => 'ApplePayDomain',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApplePayDomain',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Application' => 
  array (
    'type' => 'class',
    'classname' => 'Application',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Application',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApplicationFee' => 
  array (
    'type' => 'class',
    'classname' => 'ApplicationFee',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApplicationFee',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ApplicationFeeRefund' => 
  array (
    'type' => 'class',
    'classname' => 'ApplicationFeeRefund',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ApplicationFeeRefund',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Apps\\Secret' => 
  array (
    'type' => 'class',
    'classname' => 'Secret',
    'isabstract' => false,
    'namespace' => 'Stripe\\Apps',
    'extends' => 'AmeliaVendor\\Stripe\\Apps\\Secret',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Balance' => 
  array (
    'type' => 'class',
    'classname' => 'Balance',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Balance',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\BalanceTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'BalanceTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\BalanceTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\BankAccount' => 
  array (
    'type' => 'class',
    'classname' => 'BankAccount',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\BankAccount',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\BaseStripeClient' => 
  array (
    'type' => 'class',
    'classname' => 'BaseStripeClient',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\BaseStripeClient',
    'implements' => 
    array (
      0 => 'Stripe\\StripeClientInterface',
      1 => 'Stripe\\StripeStreamingClientInterface',
    ),
  ),
  'Stripe\\Billing\\Alert' => 
  array (
    'type' => 'class',
    'classname' => 'Alert',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\Alert',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\AlertTriggered' => 
  array (
    'type' => 'class',
    'classname' => 'AlertTriggered',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\AlertTriggered',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\CreditBalanceSummary' => 
  array (
    'type' => 'class',
    'classname' => 'CreditBalanceSummary',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\CreditBalanceSummary',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\CreditBalanceTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'CreditBalanceTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\CreditBalanceTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\CreditGrant' => 
  array (
    'type' => 'class',
    'classname' => 'CreditGrant',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\CreditGrant',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\Meter' => 
  array (
    'type' => 'class',
    'classname' => 'Meter',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\Meter',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\MeterEvent' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEvent',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\MeterEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\MeterEventAdjustment' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventAdjustment',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\MeterEventAdjustment',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Billing\\MeterEventSummary' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventSummary',
    'isabstract' => false,
    'namespace' => 'Stripe\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Billing\\MeterEventSummary',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\BillingPortal\\Configuration' => 
  array (
    'type' => 'class',
    'classname' => 'Configuration',
    'isabstract' => false,
    'namespace' => 'Stripe\\BillingPortal',
    'extends' => 'AmeliaVendor\\Stripe\\BillingPortal\\Configuration',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\BillingPortal\\Session' => 
  array (
    'type' => 'class',
    'classname' => 'Session',
    'isabstract' => false,
    'namespace' => 'Stripe\\BillingPortal',
    'extends' => 'AmeliaVendor\\Stripe\\BillingPortal\\Session',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Capability' => 
  array (
    'type' => 'class',
    'classname' => 'Capability',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Capability',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Card' => 
  array (
    'type' => 'class',
    'classname' => 'Card',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Card',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CashBalance' => 
  array (
    'type' => 'class',
    'classname' => 'CashBalance',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CashBalance',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Charge' => 
  array (
    'type' => 'class',
    'classname' => 'Charge',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Charge',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Checkout\\Session' => 
  array (
    'type' => 'class',
    'classname' => 'Session',
    'isabstract' => false,
    'namespace' => 'Stripe\\Checkout',
    'extends' => 'AmeliaVendor\\Stripe\\Checkout\\Session',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Climate\\Order' => 
  array (
    'type' => 'class',
    'classname' => 'Order',
    'isabstract' => false,
    'namespace' => 'Stripe\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Climate\\Order',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Climate\\Product' => 
  array (
    'type' => 'class',
    'classname' => 'Product',
    'isabstract' => false,
    'namespace' => 'Stripe\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Climate\\Product',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Climate\\Supplier' => 
  array (
    'type' => 'class',
    'classname' => 'Supplier',
    'isabstract' => false,
    'namespace' => 'Stripe\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Climate\\Supplier',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Collection',
    'implements' => 
    array (
      0 => 'Countable',
      1 => 'IteratorAggregate',
    ),
  ),
  'Stripe\\ConfirmationToken' => 
  array (
    'type' => 'class',
    'classname' => 'ConfirmationToken',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ConfirmationToken',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ConnectCollectionTransfer' => 
  array (
    'type' => 'class',
    'classname' => 'ConnectCollectionTransfer',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ConnectCollectionTransfer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CountrySpec' => 
  array (
    'type' => 'class',
    'classname' => 'CountrySpec',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CountrySpec',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Coupon' => 
  array (
    'type' => 'class',
    'classname' => 'Coupon',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Coupon',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CreditNote' => 
  array (
    'type' => 'class',
    'classname' => 'CreditNote',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CreditNote',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CreditNoteLineItem' => 
  array (
    'type' => 'class',
    'classname' => 'CreditNoteLineItem',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CreditNoteLineItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Customer' => 
  array (
    'type' => 'class',
    'classname' => 'Customer',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Customer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CustomerBalanceTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerBalanceTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CustomerBalanceTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CustomerCashBalanceTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerCashBalanceTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CustomerCashBalanceTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\CustomerSession' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerSession',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\CustomerSession',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Discount' => 
  array (
    'type' => 'class',
    'classname' => 'Discount',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Discount',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Dispute' => 
  array (
    'type' => 'class',
    'classname' => 'Dispute',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Dispute',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Entitlements\\ActiveEntitlement' => 
  array (
    'type' => 'class',
    'classname' => 'ActiveEntitlement',
    'isabstract' => false,
    'namespace' => 'Stripe\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Entitlements\\ActiveEntitlement',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Entitlements\\ActiveEntitlementSummary' => 
  array (
    'type' => 'class',
    'classname' => 'ActiveEntitlementSummary',
    'isabstract' => false,
    'namespace' => 'Stripe\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Entitlements\\ActiveEntitlementSummary',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Entitlements\\Feature' => 
  array (
    'type' => 'class',
    'classname' => 'Feature',
    'isabstract' => false,
    'namespace' => 'Stripe\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Entitlements\\Feature',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\EphemeralKey' => 
  array (
    'type' => 'class',
    'classname' => 'EphemeralKey',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\EphemeralKey',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ErrorObject' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorObject',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ErrorObject',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Event' => 
  array (
    'type' => 'class',
    'classname' => 'Event',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Event',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\EventData\\V1BillingMeterErrorReportTriggeredEventData' => 
  array (
    'type' => 'class',
    'classname' => 'V1BillingMeterErrorReportTriggeredEventData',
    'isabstract' => false,
    'namespace' => 'Stripe\\EventData',
    'extends' => 'AmeliaVendor\\Stripe\\EventData\\V1BillingMeterErrorReportTriggeredEventData',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\EventData\\V1BillingMeterNoMeterFoundEventData' => 
  array (
    'type' => 'class',
    'classname' => 'V1BillingMeterNoMeterFoundEventData',
    'isabstract' => false,
    'namespace' => 'Stripe\\EventData',
    'extends' => 'AmeliaVendor\\Stripe\\EventData\\V1BillingMeterNoMeterFoundEventData',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Events\\V1BillingMeterErrorReportTriggeredEvent' => 
  array (
    'type' => 'class',
    'classname' => 'V1BillingMeterErrorReportTriggeredEvent',
    'isabstract' => false,
    'namespace' => 'Stripe\\Events',
    'extends' => 'AmeliaVendor\\Stripe\\Events\\V1BillingMeterErrorReportTriggeredEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Events\\V1BillingMeterNoMeterFoundEvent' => 
  array (
    'type' => 'class',
    'classname' => 'V1BillingMeterNoMeterFoundEvent',
    'isabstract' => false,
    'namespace' => 'Stripe\\Events',
    'extends' => 'AmeliaVendor\\Stripe\\Events\\V1BillingMeterNoMeterFoundEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Events\\V2CoreEventDestinationPingEvent' => 
  array (
    'type' => 'class',
    'classname' => 'V2CoreEventDestinationPingEvent',
    'isabstract' => false,
    'namespace' => 'Stripe\\Events',
    'extends' => 'AmeliaVendor\\Stripe\\Events\\V2CoreEventDestinationPingEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\ApiConnectionException' => 
  array (
    'type' => 'class',
    'classname' => 'ApiConnectionException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\ApiConnectionException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\ApiErrorException' => 
  array (
    'type' => 'class',
    'classname' => 'ApiErrorException',
    'isabstract' => true,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\ApiErrorException',
    'implements' => 
    array (
      0 => 'Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\AuthenticationException' => 
  array (
    'type' => 'class',
    'classname' => 'AuthenticationException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\AuthenticationException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\BadMethodCallException' => 
  array (
    'type' => 'class',
    'classname' => 'BadMethodCallException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\BadMethodCallException',
    'implements' => 
    array (
      0 => 'Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\CardException' => 
  array (
    'type' => 'class',
    'classname' => 'CardException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\CardException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\IdempotencyException' => 
  array (
    'type' => 'class',
    'classname' => 'IdempotencyException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\IdempotencyException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\InvalidArgumentException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidArgumentException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\InvalidArgumentException',
    'implements' => 
    array (
      0 => 'Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\InvalidRequestException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidRequestException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\InvalidRequestException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\InvalidClientException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidClientException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\InvalidClientException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\InvalidGrantException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidGrantException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\InvalidGrantException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\InvalidRequestException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidRequestException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\InvalidRequestException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\InvalidScopeException' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidScopeException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\InvalidScopeException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\OAuthErrorException' => 
  array (
    'type' => 'class',
    'classname' => 'OAuthErrorException',
    'isabstract' => true,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\OAuthErrorException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\UnknownOAuthErrorException' => 
  array (
    'type' => 'class',
    'classname' => 'UnknownOAuthErrorException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\UnknownOAuthErrorException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\UnsupportedGrantTypeException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedGrantTypeException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\UnsupportedGrantTypeException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\OAuth\\UnsupportedResponseTypeException' => 
  array (
    'type' => 'class',
    'classname' => 'UnsupportedResponseTypeException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\UnsupportedResponseTypeException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\PermissionException' => 
  array (
    'type' => 'class',
    'classname' => 'PermissionException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\PermissionException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\RateLimitException' => 
  array (
    'type' => 'class',
    'classname' => 'RateLimitException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\RateLimitException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\SignatureVerificationException' => 
  array (
    'type' => 'class',
    'classname' => 'SignatureVerificationException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\SignatureVerificationException',
    'implements' => 
    array (
      0 => 'Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\TemporarySessionExpiredException' => 
  array (
    'type' => 'class',
    'classname' => 'TemporarySessionExpiredException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\TemporarySessionExpiredException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Exception\\UnexpectedValueException' => 
  array (
    'type' => 'class',
    'classname' => 'UnexpectedValueException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\UnexpectedValueException',
    'implements' => 
    array (
      0 => 'Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\UnknownApiErrorException' => 
  array (
    'type' => 'class',
    'classname' => 'UnknownApiErrorException',
    'isabstract' => false,
    'namespace' => 'Stripe\\Exception',
    'extends' => 'AmeliaVendor\\Stripe\\Exception\\UnknownApiErrorException',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ExchangeRate' => 
  array (
    'type' => 'class',
    'classname' => 'ExchangeRate',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ExchangeRate',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\File',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FileLink' => 
  array (
    'type' => 'class',
    'classname' => 'FileLink',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\FileLink',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FinancialConnections\\Account' => 
  array (
    'type' => 'class',
    'classname' => 'Account',
    'isabstract' => false,
    'namespace' => 'Stripe\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\FinancialConnections\\Account',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FinancialConnections\\AccountOwner' => 
  array (
    'type' => 'class',
    'classname' => 'AccountOwner',
    'isabstract' => false,
    'namespace' => 'Stripe\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\FinancialConnections\\AccountOwner',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FinancialConnections\\AccountOwnership' => 
  array (
    'type' => 'class',
    'classname' => 'AccountOwnership',
    'isabstract' => false,
    'namespace' => 'Stripe\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\FinancialConnections\\AccountOwnership',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FinancialConnections\\Session' => 
  array (
    'type' => 'class',
    'classname' => 'Session',
    'isabstract' => false,
    'namespace' => 'Stripe\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\FinancialConnections\\Session',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FinancialConnections\\Transaction' => 
  array (
    'type' => 'class',
    'classname' => 'Transaction',
    'isabstract' => false,
    'namespace' => 'Stripe\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\FinancialConnections\\Transaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Forwarding\\Request' => 
  array (
    'type' => 'class',
    'classname' => 'Request',
    'isabstract' => false,
    'namespace' => 'Stripe\\Forwarding',
    'extends' => 'AmeliaVendor\\Stripe\\Forwarding\\Request',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\FundingInstructions' => 
  array (
    'type' => 'class',
    'classname' => 'FundingInstructions',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\FundingInstructions',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\HttpClient\\CurlClient' => 
  array (
    'type' => 'class',
    'classname' => 'CurlClient',
    'isabstract' => false,
    'namespace' => 'Stripe\\HttpClient',
    'extends' => 'AmeliaVendor\\Stripe\\HttpClient\\CurlClient',
    'implements' => 
    array (
      0 => 'Stripe\\HttpClient\\ClientInterface',
      1 => 'Stripe\\HttpClient\\StreamingClientInterface',
    ),
  ),
  'Stripe\\Identity\\VerificationReport' => 
  array (
    'type' => 'class',
    'classname' => 'VerificationReport',
    'isabstract' => false,
    'namespace' => 'Stripe\\Identity',
    'extends' => 'AmeliaVendor\\Stripe\\Identity\\VerificationReport',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Identity\\VerificationSession' => 
  array (
    'type' => 'class',
    'classname' => 'VerificationSession',
    'isabstract' => false,
    'namespace' => 'Stripe\\Identity',
    'extends' => 'AmeliaVendor\\Stripe\\Identity\\VerificationSession',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Invoice' => 
  array (
    'type' => 'class',
    'classname' => 'Invoice',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Invoice',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\InvoiceItem' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceItem',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\InvoiceItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\InvoiceLineItem' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceLineItem',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\InvoiceLineItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\InvoicePayment' => 
  array (
    'type' => 'class',
    'classname' => 'InvoicePayment',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\InvoicePayment',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\InvoiceRenderingTemplate' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceRenderingTemplate',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\InvoiceRenderingTemplate',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Authorization' => 
  array (
    'type' => 'class',
    'classname' => 'Authorization',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Authorization',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Card' => 
  array (
    'type' => 'class',
    'classname' => 'Card',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Card',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\CardDetails' => 
  array (
    'type' => 'class',
    'classname' => 'CardDetails',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\CardDetails',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Cardholder' => 
  array (
    'type' => 'class',
    'classname' => 'Cardholder',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Cardholder',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Dispute' => 
  array (
    'type' => 'class',
    'classname' => 'Dispute',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Dispute',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\PersonalizationDesign' => 
  array (
    'type' => 'class',
    'classname' => 'PersonalizationDesign',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\PersonalizationDesign',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\PhysicalBundle' => 
  array (
    'type' => 'class',
    'classname' => 'PhysicalBundle',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\PhysicalBundle',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Token' => 
  array (
    'type' => 'class',
    'classname' => 'Token',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Token',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Issuing\\Transaction' => 
  array (
    'type' => 'class',
    'classname' => 'Transaction',
    'isabstract' => false,
    'namespace' => 'Stripe\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Issuing\\Transaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\LineItem' => 
  array (
    'type' => 'class',
    'classname' => 'LineItem',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\LineItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\LoginLink' => 
  array (
    'type' => 'class',
    'classname' => 'LoginLink',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\LoginLink',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Mandate' => 
  array (
    'type' => 'class',
    'classname' => 'Mandate',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Mandate',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\OAuth' => 
  array (
    'type' => 'class',
    'classname' => 'OAuth',
    'isabstract' => true,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\OAuth',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\OAuthErrorObject' => 
  array (
    'type' => 'class',
    'classname' => 'OAuthErrorObject',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\OAuthErrorObject',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PaymentIntent' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentIntent',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PaymentIntent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PaymentLink' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentLink',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PaymentLink',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PaymentMethod' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethod',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PaymentMethod',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PaymentMethodConfiguration' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethodConfiguration',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PaymentMethodConfiguration',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PaymentMethodDomain' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethodDomain',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PaymentMethodDomain',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Payout' => 
  array (
    'type' => 'class',
    'classname' => 'Payout',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Payout',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Person' => 
  array (
    'type' => 'class',
    'classname' => 'Person',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Person',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Plan' => 
  array (
    'type' => 'class',
    'classname' => 'Plan',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Plan',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Price' => 
  array (
    'type' => 'class',
    'classname' => 'Price',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Price',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Product' => 
  array (
    'type' => 'class',
    'classname' => 'Product',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Product',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ProductFeature' => 
  array (
    'type' => 'class',
    'classname' => 'ProductFeature',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ProductFeature',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\PromotionCode' => 
  array (
    'type' => 'class',
    'classname' => 'PromotionCode',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\PromotionCode',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Quote' => 
  array (
    'type' => 'class',
    'classname' => 'Quote',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Quote',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Radar\\EarlyFraudWarning' => 
  array (
    'type' => 'class',
    'classname' => 'EarlyFraudWarning',
    'isabstract' => false,
    'namespace' => 'Stripe\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Radar\\EarlyFraudWarning',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Radar\\ValueList' => 
  array (
    'type' => 'class',
    'classname' => 'ValueList',
    'isabstract' => false,
    'namespace' => 'Stripe\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Radar\\ValueList',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Radar\\ValueListItem' => 
  array (
    'type' => 'class',
    'classname' => 'ValueListItem',
    'isabstract' => false,
    'namespace' => 'Stripe\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Radar\\ValueListItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Reason' => 
  array (
    'type' => 'class',
    'classname' => 'Reason',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Reason',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\RecipientTransfer' => 
  array (
    'type' => 'class',
    'classname' => 'RecipientTransfer',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\RecipientTransfer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Refund' => 
  array (
    'type' => 'class',
    'classname' => 'Refund',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Refund',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\RelatedObject' => 
  array (
    'type' => 'class',
    'classname' => 'RelatedObject',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\RelatedObject',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Reporting\\ReportRun' => 
  array (
    'type' => 'class',
    'classname' => 'ReportRun',
    'isabstract' => false,
    'namespace' => 'Stripe\\Reporting',
    'extends' => 'AmeliaVendor\\Stripe\\Reporting\\ReportRun',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Reporting\\ReportType' => 
  array (
    'type' => 'class',
    'classname' => 'ReportType',
    'isabstract' => false,
    'namespace' => 'Stripe\\Reporting',
    'extends' => 'AmeliaVendor\\Stripe\\Reporting\\ReportType',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\RequestTelemetry' => 
  array (
    'type' => 'class',
    'classname' => 'RequestTelemetry',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\RequestTelemetry',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ReserveTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'ReserveTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ReserveTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Review' => 
  array (
    'type' => 'class',
    'classname' => 'Review',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Review',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SearchResult' => 
  array (
    'type' => 'class',
    'classname' => 'SearchResult',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SearchResult',
    'implements' => 
    array (
      0 => 'Countable',
      1 => 'IteratorAggregate',
    ),
  ),
  'Stripe\\Service\\AbstractService' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractService',
    'isabstract' => true,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\AbstractService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\AbstractServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'AbstractServiceFactory',
    'isabstract' => true,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\AbstractServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\AccountLinkService' => 
  array (
    'type' => 'class',
    'classname' => 'AccountLinkService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\AccountLinkService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\AccountService' => 
  array (
    'type' => 'class',
    'classname' => 'AccountService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\AccountService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\AccountSessionService' => 
  array (
    'type' => 'class',
    'classname' => 'AccountSessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\AccountSessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ApplePayDomainService' => 
  array (
    'type' => 'class',
    'classname' => 'ApplePayDomainService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ApplePayDomainService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ApplicationFeeService' => 
  array (
    'type' => 'class',
    'classname' => 'ApplicationFeeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ApplicationFeeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Apps\\AppsServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'AppsServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Apps',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Apps\\AppsServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Apps\\SecretService' => 
  array (
    'type' => 'class',
    'classname' => 'SecretService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Apps',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Apps\\SecretService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\BalanceService' => 
  array (
    'type' => 'class',
    'classname' => 'BalanceService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\BalanceService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\BalanceTransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'BalanceTransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\BalanceTransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\AlertService' => 
  array (
    'type' => 'class',
    'classname' => 'AlertService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\AlertService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\BillingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'BillingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\BillingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\CreditBalanceSummaryService' => 
  array (
    'type' => 'class',
    'classname' => 'CreditBalanceSummaryService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\CreditBalanceSummaryService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\CreditBalanceTransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'CreditBalanceTransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\CreditBalanceTransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\CreditGrantService' => 
  array (
    'type' => 'class',
    'classname' => 'CreditGrantService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\CreditGrantService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\MeterEventAdjustmentService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventAdjustmentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\MeterEventAdjustmentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\MeterEventService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\MeterEventService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Billing\\MeterService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Billing\\MeterService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\BillingPortal\\BillingPortalServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'BillingPortalServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\BillingPortal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\BillingPortal\\BillingPortalServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\BillingPortal\\ConfigurationService' => 
  array (
    'type' => 'class',
    'classname' => 'ConfigurationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\BillingPortal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\BillingPortal\\ConfigurationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\BillingPortal\\SessionService' => 
  array (
    'type' => 'class',
    'classname' => 'SessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\BillingPortal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\BillingPortal\\SessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ChargeService' => 
  array (
    'type' => 'class',
    'classname' => 'ChargeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ChargeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Checkout\\CheckoutServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'CheckoutServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Checkout',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Checkout\\CheckoutServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Checkout\\SessionService' => 
  array (
    'type' => 'class',
    'classname' => 'SessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Checkout',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Checkout\\SessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Climate\\ClimateServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'ClimateServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Climate\\ClimateServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Climate\\OrderService' => 
  array (
    'type' => 'class',
    'classname' => 'OrderService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Climate\\OrderService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Climate\\ProductService' => 
  array (
    'type' => 'class',
    'classname' => 'ProductService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Climate\\ProductService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Climate\\SupplierService' => 
  array (
    'type' => 'class',
    'classname' => 'SupplierService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Climate',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Climate\\SupplierService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ConfirmationTokenService' => 
  array (
    'type' => 'class',
    'classname' => 'ConfirmationTokenService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ConfirmationTokenService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CoreServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'CoreServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CoreServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CountrySpecService' => 
  array (
    'type' => 'class',
    'classname' => 'CountrySpecService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CountrySpecService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CouponService' => 
  array (
    'type' => 'class',
    'classname' => 'CouponService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CouponService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CreditNoteService' => 
  array (
    'type' => 'class',
    'classname' => 'CreditNoteService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CreditNoteService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CustomerService' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CustomerService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\CustomerSessionService' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerSessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\CustomerSessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\DisputeService' => 
  array (
    'type' => 'class',
    'classname' => 'DisputeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\DisputeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Entitlements\\ActiveEntitlementService' => 
  array (
    'type' => 'class',
    'classname' => 'ActiveEntitlementService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Entitlements\\ActiveEntitlementService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Entitlements\\EntitlementsServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'EntitlementsServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Entitlements\\EntitlementsServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Entitlements\\FeatureService' => 
  array (
    'type' => 'class',
    'classname' => 'FeatureService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Entitlements',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Entitlements\\FeatureService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\EphemeralKeyService' => 
  array (
    'type' => 'class',
    'classname' => 'EphemeralKeyService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\EphemeralKeyService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\EventService' => 
  array (
    'type' => 'class',
    'classname' => 'EventService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\EventService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ExchangeRateService' => 
  array (
    'type' => 'class',
    'classname' => 'ExchangeRateService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ExchangeRateService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FileLinkService' => 
  array (
    'type' => 'class',
    'classname' => 'FileLinkService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FileLinkService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FileService' => 
  array (
    'type' => 'class',
    'classname' => 'FileService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FileService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FinancialConnections\\AccountService' => 
  array (
    'type' => 'class',
    'classname' => 'AccountService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FinancialConnections\\AccountService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FinancialConnections\\FinancialConnectionsServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'FinancialConnectionsServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FinancialConnections\\FinancialConnectionsServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FinancialConnections\\SessionService' => 
  array (
    'type' => 'class',
    'classname' => 'SessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FinancialConnections\\SessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\FinancialConnections\\TransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\FinancialConnections',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\FinancialConnections\\TransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Forwarding\\ForwardingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'ForwardingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Forwarding',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Forwarding\\ForwardingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Forwarding\\RequestService' => 
  array (
    'type' => 'class',
    'classname' => 'RequestService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Forwarding',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Forwarding\\RequestService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Identity\\IdentityServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'IdentityServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Identity',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Identity\\IdentityServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Identity\\VerificationReportService' => 
  array (
    'type' => 'class',
    'classname' => 'VerificationReportService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Identity',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Identity\\VerificationReportService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Identity\\VerificationSessionService' => 
  array (
    'type' => 'class',
    'classname' => 'VerificationSessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Identity',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Identity\\VerificationSessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\InvoiceItemService' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceItemService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\InvoiceItemService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\InvoicePaymentService' => 
  array (
    'type' => 'class',
    'classname' => 'InvoicePaymentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\InvoicePaymentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\InvoiceRenderingTemplateService' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceRenderingTemplateService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\InvoiceRenderingTemplateService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\InvoiceService' => 
  array (
    'type' => 'class',
    'classname' => 'InvoiceService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\InvoiceService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\AuthorizationService' => 
  array (
    'type' => 'class',
    'classname' => 'AuthorizationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\AuthorizationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\CardService' => 
  array (
    'type' => 'class',
    'classname' => 'CardService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\CardService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\CardholderService' => 
  array (
    'type' => 'class',
    'classname' => 'CardholderService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\CardholderService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\DisputeService' => 
  array (
    'type' => 'class',
    'classname' => 'DisputeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\DisputeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\IssuingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'IssuingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\IssuingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\PersonalizationDesignService' => 
  array (
    'type' => 'class',
    'classname' => 'PersonalizationDesignService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\PersonalizationDesignService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\PhysicalBundleService' => 
  array (
    'type' => 'class',
    'classname' => 'PhysicalBundleService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\PhysicalBundleService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\TokenService' => 
  array (
    'type' => 'class',
    'classname' => 'TokenService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\TokenService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Issuing\\TransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Issuing\\TransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\MandateService' => 
  array (
    'type' => 'class',
    'classname' => 'MandateService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\MandateService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\OAuthService' => 
  array (
    'type' => 'class',
    'classname' => 'OAuthService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\OAuthService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PaymentIntentService' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentIntentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PaymentIntentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PaymentLinkService' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentLinkService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PaymentLinkService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PaymentMethodConfigurationService' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethodConfigurationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PaymentMethodConfigurationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PaymentMethodDomainService' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethodDomainService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PaymentMethodDomainService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PaymentMethodService' => 
  array (
    'type' => 'class',
    'classname' => 'PaymentMethodService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PaymentMethodService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PayoutService' => 
  array (
    'type' => 'class',
    'classname' => 'PayoutService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PayoutService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PlanService' => 
  array (
    'type' => 'class',
    'classname' => 'PlanService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PlanService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PriceService' => 
  array (
    'type' => 'class',
    'classname' => 'PriceService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PriceService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ProductService' => 
  array (
    'type' => 'class',
    'classname' => 'ProductService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ProductService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\PromotionCodeService' => 
  array (
    'type' => 'class',
    'classname' => 'PromotionCodeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\PromotionCodeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\QuoteService' => 
  array (
    'type' => 'class',
    'classname' => 'QuoteService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\QuoteService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Radar\\EarlyFraudWarningService' => 
  array (
    'type' => 'class',
    'classname' => 'EarlyFraudWarningService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Radar\\EarlyFraudWarningService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Radar\\RadarServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'RadarServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Radar\\RadarServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Radar\\ValueListItemService' => 
  array (
    'type' => 'class',
    'classname' => 'ValueListItemService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Radar\\ValueListItemService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Radar\\ValueListService' => 
  array (
    'type' => 'class',
    'classname' => 'ValueListService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Radar',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Radar\\ValueListService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\RefundService' => 
  array (
    'type' => 'class',
    'classname' => 'RefundService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\RefundService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Reporting\\ReportRunService' => 
  array (
    'type' => 'class',
    'classname' => 'ReportRunService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Reporting',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Reporting\\ReportRunService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Reporting\\ReportTypeService' => 
  array (
    'type' => 'class',
    'classname' => 'ReportTypeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Reporting',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Reporting\\ReportTypeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Reporting\\ReportingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'ReportingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Reporting',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Reporting\\ReportingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ReviewService' => 
  array (
    'type' => 'class',
    'classname' => 'ReviewService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ReviewService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SetupAttemptService' => 
  array (
    'type' => 'class',
    'classname' => 'SetupAttemptService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SetupAttemptService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SetupIntentService' => 
  array (
    'type' => 'class',
    'classname' => 'SetupIntentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SetupIntentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\ShippingRateService' => 
  array (
    'type' => 'class',
    'classname' => 'ShippingRateService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\ShippingRateService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Sigma\\ScheduledQueryRunService' => 
  array (
    'type' => 'class',
    'classname' => 'ScheduledQueryRunService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Sigma',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Sigma\\ScheduledQueryRunService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Sigma\\SigmaServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'SigmaServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Sigma',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Sigma\\SigmaServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SourceService' => 
  array (
    'type' => 'class',
    'classname' => 'SourceService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SourceService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SubscriptionItemService' => 
  array (
    'type' => 'class',
    'classname' => 'SubscriptionItemService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SubscriptionItemService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SubscriptionScheduleService' => 
  array (
    'type' => 'class',
    'classname' => 'SubscriptionScheduleService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SubscriptionScheduleService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\SubscriptionService' => 
  array (
    'type' => 'class',
    'classname' => 'SubscriptionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\SubscriptionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Tax\\CalculationService' => 
  array (
    'type' => 'class',
    'classname' => 'CalculationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Tax\\CalculationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Tax\\RegistrationService' => 
  array (
    'type' => 'class',
    'classname' => 'RegistrationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Tax\\RegistrationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Tax\\SettingsService' => 
  array (
    'type' => 'class',
    'classname' => 'SettingsService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Tax\\SettingsService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Tax\\TaxServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TaxServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Tax\\TaxServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Tax\\TransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Tax\\TransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TaxCodeService' => 
  array (
    'type' => 'class',
    'classname' => 'TaxCodeService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TaxCodeService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TaxIdService' => 
  array (
    'type' => 'class',
    'classname' => 'TaxIdService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TaxIdService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TaxRateService' => 
  array (
    'type' => 'class',
    'classname' => 'TaxRateService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TaxRateService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Terminal\\ConfigurationService' => 
  array (
    'type' => 'class',
    'classname' => 'ConfigurationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Terminal\\ConfigurationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Terminal\\ConnectionTokenService' => 
  array (
    'type' => 'class',
    'classname' => 'ConnectionTokenService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Terminal\\ConnectionTokenService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Terminal\\LocationService' => 
  array (
    'type' => 'class',
    'classname' => 'LocationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Terminal\\LocationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Terminal\\ReaderService' => 
  array (
    'type' => 'class',
    'classname' => 'ReaderService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Terminal\\ReaderService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Terminal\\TerminalServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TerminalServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Terminal\\TerminalServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\ConfirmationTokenService' => 
  array (
    'type' => 'class',
    'classname' => 'ConfirmationTokenService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\ConfirmationTokenService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\CustomerService' => 
  array (
    'type' => 'class',
    'classname' => 'CustomerService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\CustomerService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Issuing\\AuthorizationService' => 
  array (
    'type' => 'class',
    'classname' => 'AuthorizationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Issuing\\AuthorizationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Issuing\\CardService' => 
  array (
    'type' => 'class',
    'classname' => 'CardService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Issuing\\CardService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Issuing\\IssuingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'IssuingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Issuing\\IssuingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Issuing\\PersonalizationDesignService' => 
  array (
    'type' => 'class',
    'classname' => 'PersonalizationDesignService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Issuing\\PersonalizationDesignService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Issuing\\TransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Issuing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Issuing\\TransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\RefundService' => 
  array (
    'type' => 'class',
    'classname' => 'RefundService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\RefundService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Terminal\\ReaderService' => 
  array (
    'type' => 'class',
    'classname' => 'ReaderService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Terminal\\ReaderService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Terminal\\TerminalServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TerminalServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Terminal\\TerminalServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\TestClockService' => 
  array (
    'type' => 'class',
    'classname' => 'TestClockService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\TestClockService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\TestHelpersServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TestHelpersServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\TestHelpersServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\InboundTransferService' => 
  array (
    'type' => 'class',
    'classname' => 'InboundTransferService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\InboundTransferService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\OutboundPaymentService' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundPaymentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\OutboundPaymentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\OutboundTransferService' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundTransferService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\OutboundTransferService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\ReceivedCreditService' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedCreditService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\ReceivedCreditService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\ReceivedDebitService' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedDebitService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\ReceivedDebitService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TestHelpers\\Treasury\\TreasuryServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TreasuryServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\TestHelpers\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TestHelpers\\Treasury\\TreasuryServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TokenService' => 
  array (
    'type' => 'class',
    'classname' => 'TokenService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TokenService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TopupService' => 
  array (
    'type' => 'class',
    'classname' => 'TopupService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TopupService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\TransferService' => 
  array (
    'type' => 'class',
    'classname' => 'TransferService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\TransferService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\CreditReversalService' => 
  array (
    'type' => 'class',
    'classname' => 'CreditReversalService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\CreditReversalService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\DebitReversalService' => 
  array (
    'type' => 'class',
    'classname' => 'DebitReversalService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\DebitReversalService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\FinancialAccountService' => 
  array (
    'type' => 'class',
    'classname' => 'FinancialAccountService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\FinancialAccountService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\InboundTransferService' => 
  array (
    'type' => 'class',
    'classname' => 'InboundTransferService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\InboundTransferService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\OutboundPaymentService' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundPaymentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\OutboundPaymentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\OutboundTransferService' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundTransferService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\OutboundTransferService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\ReceivedCreditService' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedCreditService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\ReceivedCreditService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\ReceivedDebitService' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedDebitService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\ReceivedDebitService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\TransactionEntryService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionEntryService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\TransactionEntryService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\TransactionService' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\TransactionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\Treasury\\TreasuryServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'TreasuryServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\Treasury\\TreasuryServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Billing\\BillingServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'BillingServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Billing\\BillingServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Billing\\MeterEventAdjustmentService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventAdjustmentService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Billing\\MeterEventAdjustmentService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Billing\\MeterEventService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Billing\\MeterEventService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Billing\\MeterEventSessionService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventSessionService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Billing\\MeterEventSessionService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Billing\\MeterEventStreamService' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventStreamService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Billing\\MeterEventStreamService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Core\\CoreServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'CoreServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Core',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Core\\CoreServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Core\\EventDestinationService' => 
  array (
    'type' => 'class',
    'classname' => 'EventDestinationService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Core',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Core\\EventDestinationService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\Core\\EventService' => 
  array (
    'type' => 'class',
    'classname' => 'EventService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2\\Core',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\Core\\EventService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\V2\\V2ServiceFactory' => 
  array (
    'type' => 'class',
    'classname' => 'V2ServiceFactory',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service\\V2',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\V2\\V2ServiceFactory',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Service\\WebhookEndpointService' => 
  array (
    'type' => 'class',
    'classname' => 'WebhookEndpointService',
    'isabstract' => false,
    'namespace' => 'Stripe\\Service',
    'extends' => 'AmeliaVendor\\Stripe\\Service\\WebhookEndpointService',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SetupAttempt' => 
  array (
    'type' => 'class',
    'classname' => 'SetupAttempt',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SetupAttempt',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SetupIntent' => 
  array (
    'type' => 'class',
    'classname' => 'SetupIntent',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SetupIntent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ShippingRate' => 
  array (
    'type' => 'class',
    'classname' => 'ShippingRate',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ShippingRate',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Sigma\\ScheduledQueryRun' => 
  array (
    'type' => 'class',
    'classname' => 'ScheduledQueryRun',
    'isabstract' => false,
    'namespace' => 'Stripe\\Sigma',
    'extends' => 'AmeliaVendor\\Stripe\\Sigma\\ScheduledQueryRun',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SingletonApiResource' => 
  array (
    'type' => 'class',
    'classname' => 'SingletonApiResource',
    'isabstract' => true,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SingletonApiResource',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Source' => 
  array (
    'type' => 'class',
    'classname' => 'Source',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Source',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SourceMandateNotification' => 
  array (
    'type' => 'class',
    'classname' => 'SourceMandateNotification',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SourceMandateNotification',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SourceTransaction' => 
  array (
    'type' => 'class',
    'classname' => 'SourceTransaction',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SourceTransaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Stripe' => 
  array (
    'type' => 'class',
    'classname' => 'Stripe',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Stripe',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\StripeClient' => 
  array (
    'type' => 'class',
    'classname' => 'StripeClient',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\StripeClient',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\StripeObject' => 
  array (
    'type' => 'class',
    'classname' => 'StripeObject',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\StripeObject',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Countable',
      2 => 'JsonSerializable',
    ),
  ),
  'Stripe\\Subscription' => 
  array (
    'type' => 'class',
    'classname' => 'Subscription',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Subscription',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SubscriptionItem' => 
  array (
    'type' => 'class',
    'classname' => 'SubscriptionItem',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SubscriptionItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\SubscriptionSchedule' => 
  array (
    'type' => 'class',
    'classname' => 'SubscriptionSchedule',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\SubscriptionSchedule',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\Calculation' => 
  array (
    'type' => 'class',
    'classname' => 'Calculation',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\Calculation',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\CalculationLineItem' => 
  array (
    'type' => 'class',
    'classname' => 'CalculationLineItem',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\CalculationLineItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\Registration' => 
  array (
    'type' => 'class',
    'classname' => 'Registration',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\Registration',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\Settings',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\Transaction' => 
  array (
    'type' => 'class',
    'classname' => 'Transaction',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\Transaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Tax\\TransactionLineItem' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionLineItem',
    'isabstract' => false,
    'namespace' => 'Stripe\\Tax',
    'extends' => 'AmeliaVendor\\Stripe\\Tax\\TransactionLineItem',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TaxCode' => 
  array (
    'type' => 'class',
    'classname' => 'TaxCode',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\TaxCode',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TaxDeductedAtSource' => 
  array (
    'type' => 'class',
    'classname' => 'TaxDeductedAtSource',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\TaxDeductedAtSource',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TaxId' => 
  array (
    'type' => 'class',
    'classname' => 'TaxId',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\TaxId',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TaxRate' => 
  array (
    'type' => 'class',
    'classname' => 'TaxRate',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\TaxRate',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Terminal\\Configuration' => 
  array (
    'type' => 'class',
    'classname' => 'Configuration',
    'isabstract' => false,
    'namespace' => 'Stripe\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Terminal\\Configuration',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Terminal\\ConnectionToken' => 
  array (
    'type' => 'class',
    'classname' => 'ConnectionToken',
    'isabstract' => false,
    'namespace' => 'Stripe\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Terminal\\ConnectionToken',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Terminal\\Location' => 
  array (
    'type' => 'class',
    'classname' => 'Location',
    'isabstract' => false,
    'namespace' => 'Stripe\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Terminal\\Location',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Terminal\\Reader' => 
  array (
    'type' => 'class',
    'classname' => 'Reader',
    'isabstract' => false,
    'namespace' => 'Stripe\\Terminal',
    'extends' => 'AmeliaVendor\\Stripe\\Terminal\\Reader',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TestHelpers\\TestClock' => 
  array (
    'type' => 'class',
    'classname' => 'TestClock',
    'isabstract' => false,
    'namespace' => 'Stripe\\TestHelpers',
    'extends' => 'AmeliaVendor\\Stripe\\TestHelpers\\TestClock',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\ThinEvent' => 
  array (
    'type' => 'class',
    'classname' => 'ThinEvent',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\ThinEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Token' => 
  array (
    'type' => 'class',
    'classname' => 'Token',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Token',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Topup' => 
  array (
    'type' => 'class',
    'classname' => 'Topup',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Topup',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Transfer' => 
  array (
    'type' => 'class',
    'classname' => 'Transfer',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Transfer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\TransferReversal' => 
  array (
    'type' => 'class',
    'classname' => 'TransferReversal',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\TransferReversal',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\CreditReversal' => 
  array (
    'type' => 'class',
    'classname' => 'CreditReversal',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\CreditReversal',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\DebitReversal' => 
  array (
    'type' => 'class',
    'classname' => 'DebitReversal',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\DebitReversal',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\FinancialAccount' => 
  array (
    'type' => 'class',
    'classname' => 'FinancialAccount',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\FinancialAccount',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\FinancialAccountFeatures' => 
  array (
    'type' => 'class',
    'classname' => 'FinancialAccountFeatures',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\FinancialAccountFeatures',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\InboundTransfer' => 
  array (
    'type' => 'class',
    'classname' => 'InboundTransfer',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\InboundTransfer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\OutboundPayment' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundPayment',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\OutboundPayment',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\OutboundTransfer' => 
  array (
    'type' => 'class',
    'classname' => 'OutboundTransfer',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\OutboundTransfer',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\ReceivedCredit' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedCredit',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\ReceivedCredit',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\ReceivedDebit' => 
  array (
    'type' => 'class',
    'classname' => 'ReceivedDebit',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\ReceivedDebit',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\Transaction' => 
  array (
    'type' => 'class',
    'classname' => 'Transaction',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\Transaction',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Treasury\\TransactionEntry' => 
  array (
    'type' => 'class',
    'classname' => 'TransactionEntry',
    'isabstract' => false,
    'namespace' => 'Stripe\\Treasury',
    'extends' => 'AmeliaVendor\\Stripe\\Treasury\\TransactionEntry',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\ApiVersion' => 
  array (
    'type' => 'class',
    'classname' => 'ApiVersion',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\ApiVersion',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\CaseInsensitiveArray' => 
  array (
    'type' => 'class',
    'classname' => 'CaseInsensitiveArray',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\CaseInsensitiveArray',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Countable',
      2 => 'IteratorAggregate',
    ),
  ),
  'Stripe\\Util\\DefaultLogger' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultLogger',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\DefaultLogger',
    'implements' => 
    array (
      0 => 'Stripe\\Util\\LoggerInterface',
    ),
  ),
  'Stripe\\Util\\EventTypes' => 
  array (
    'type' => 'class',
    'classname' => 'EventTypes',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\EventTypes',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\ObjectTypes' => 
  array (
    'type' => 'class',
    'classname' => 'ObjectTypes',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\ObjectTypes',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\RandomGenerator' => 
  array (
    'type' => 'class',
    'classname' => 'RandomGenerator',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\RandomGenerator',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\RequestOptions' => 
  array (
    'type' => 'class',
    'classname' => 'RequestOptions',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\RequestOptions',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Util\\Set' => 
  array (
    'type' => 'class',
    'classname' => 'Set',
    'isabstract' => false,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\Set',
    'implements' => 
    array (
      0 => 'IteratorAggregate',
    ),
  ),
  'Stripe\\Util\\Util' => 
  array (
    'type' => 'class',
    'classname' => 'Util',
    'isabstract' => true,
    'namespace' => 'Stripe\\Util',
    'extends' => 'AmeliaVendor\\Stripe\\Util\\Util',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\V2\\Billing\\MeterEvent' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEvent',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\Billing\\MeterEvent',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\V2\\Billing\\MeterEventAdjustment' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventAdjustment',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\Billing\\MeterEventAdjustment',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\V2\\Billing\\MeterEventSession' => 
  array (
    'type' => 'class',
    'classname' => 'MeterEventSession',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2\\Billing',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\Billing\\MeterEventSession',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\V2\\Collection' => 
  array (
    'type' => 'class',
    'classname' => 'Collection',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\Collection',
    'implements' => 
    array (
      0 => 'Countable',
      1 => 'IteratorAggregate',
    ),
  ),
  'Stripe\\V2\\Event' => 
  array (
    'type' => 'class',
    'classname' => 'Event',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\Event',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\V2\\EventDestination' => 
  array (
    'type' => 'class',
    'classname' => 'EventDestination',
    'isabstract' => false,
    'namespace' => 'Stripe\\V2',
    'extends' => 'AmeliaVendor\\Stripe\\V2\\EventDestination',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\Webhook' => 
  array (
    'type' => 'class',
    'classname' => 'Webhook',
    'isabstract' => true,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\Webhook',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\WebhookEndpoint' => 
  array (
    'type' => 'class',
    'classname' => 'WebhookEndpoint',
    'isabstract' => false,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\WebhookEndpoint',
    'implements' => 
    array (
    ),
  ),
  'Stripe\\WebhookSignature' => 
  array (
    'type' => 'class',
    'classname' => 'WebhookSignature',
    'isabstract' => true,
    'namespace' => 'Stripe',
    'extends' => 'AmeliaVendor\\Stripe\\WebhookSignature',
    'implements' => 
    array (
    ),
  ),
  'WpOrg\\Requests\\Auth' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Auth',
    'namespace' => 'WpOrg\\Requests',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\WpOrg\\Requests\\Auth',
    ),
  ),
  'WpOrg\\Requests\\Proxy' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Proxy',
    'namespace' => 'WpOrg\\Requests',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\WpOrg\\Requests\\Proxy',
    ),
  ),
  'WpOrg\\Requests\\Transport' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Transport',
    'namespace' => 'WpOrg\\Requests',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\WpOrg\\Requests\\Transport',
    ),
  ),
  'Sabre\\Xml\\Element' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Element',
    'namespace' => 'Sabre\\Xml',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\Xml\\Element',
    ),
  ),
  'Google\\Auth\\CacheTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'CacheTrait',
    'namespace' => 'Google\\Auth',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\CacheTrait',
    ),
  ),
  'Google\\Auth\\IamSignerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'IamSignerTrait',
    'namespace' => 'Google\\Auth',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\IamSignerTrait',
    ),
  ),
  'Google\\Auth\\ServiceAccountSignerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'ServiceAccountSignerTrait',
    'namespace' => 'Google\\Auth',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\ServiceAccountSignerTrait',
    ),
  ),
  'Google\\Auth\\UpdateMetadataTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'UpdateMetadataTrait',
    'namespace' => 'Google\\Auth',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\UpdateMetadataTrait',
    ),
  ),
  'Monolog\\Handler\\FormattableHandlerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'FormattableHandlerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\FormattableHandlerTrait',
    ),
  ),
  'Monolog\\Handler\\ProcessableHandlerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'ProcessableHandlerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\ProcessableHandlerTrait',
    ),
  ),
  'Monolog\\Handler\\WebRequestRecognizerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'WebRequestRecognizerTrait',
    'namespace' => 'Monolog\\Handler',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\WebRequestRecognizerTrait',
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Traits\\Fingerprint' => 
  array (
    'type' => 'trait',
    'traitname' => 'Fingerprint',
    'namespace' => 'phpseclib3\\Crypt\\Common\\Traits',
    'use' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Traits\\Fingerprint',
    ),
  ),
  'phpseclib3\\Crypt\\Common\\Traits\\PasswordProtected' => 
  array (
    'type' => 'trait',
    'traitname' => 'PasswordProtected',
    'namespace' => 'phpseclib3\\Crypt\\Common\\Traits',
    'use' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\Traits\\PasswordProtected',
    ),
  ),
  'phpseclib3\\Crypt\\EC\\Formats\\Keys\\Common' => 
  array (
    'type' => 'trait',
    'traitname' => 'Common',
    'namespace' => 'phpseclib3\\Crypt\\EC\\Formats\\Keys',
    'use' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\Crypt\\EC\\Formats\\Keys\\Common',
    ),
  ),
  'phpseclib3\\System\\SSH\\Common\\Traits\\ReadBytes' => 
  array (
    'type' => 'trait',
    'traitname' => 'ReadBytes',
    'namespace' => 'phpseclib3\\System\\SSH\\Common\\Traits',
    'use' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\System\\SSH\\Common\\Traits\\ReadBytes',
    ),
  ),
  'Psr\\Log\\LoggerAwareTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'LoggerAwareTrait',
    'namespace' => 'Psr\\Log',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Log\\LoggerAwareTrait',
    ),
  ),
  'Psr\\Log\\LoggerTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'LoggerTrait',
    'namespace' => 'Psr\\Log',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Log\\LoggerTrait',
    ),
  ),
  'Sabberworm\\CSS\\Position\\Position' => 
  array (
    'type' => 'trait',
    'traitname' => 'Position',
    'namespace' => 'Sabberworm\\CSS\\Position',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\Position\\Position',
    ),
  ),
  'Sabre\\VObject\\PHPUnitAssertions' => 
  array (
    'type' => 'trait',
    'traitname' => 'PHPUnitAssertions',
    'namespace' => 'Sabre\\VObject',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\VObject\\PHPUnitAssertions',
    ),
  ),
  'Sabre\\Xml\\ContextStackTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'ContextStackTrait',
    'namespace' => 'Sabre\\Xml',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\Xml\\ContextStackTrait',
    ),
  ),
  'Stripe\\ApiOperations\\All' => 
  array (
    'type' => 'trait',
    'traitname' => 'All',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\All',
    ),
  ),
  'Stripe\\ApiOperations\\Create' => 
  array (
    'type' => 'trait',
    'traitname' => 'Create',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\Create',
    ),
  ),
  'Stripe\\ApiOperations\\Delete' => 
  array (
    'type' => 'trait',
    'traitname' => 'Delete',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\Delete',
    ),
  ),
  'Stripe\\ApiOperations\\NestedResource' => 
  array (
    'type' => 'trait',
    'traitname' => 'NestedResource',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\NestedResource',
    ),
  ),
  'Stripe\\ApiOperations\\Request' => 
  array (
    'type' => 'trait',
    'traitname' => 'Request',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\Request',
    ),
  ),
  'Stripe\\ApiOperations\\Retrieve' => 
  array (
    'type' => 'trait',
    'traitname' => 'Retrieve',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\Retrieve',
    ),
  ),
  'Stripe\\ApiOperations\\SingletonRetrieve' => 
  array (
    'type' => 'trait',
    'traitname' => 'SingletonRetrieve',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\SingletonRetrieve',
    ),
  ),
  'Stripe\\ApiOperations\\Update' => 
  array (
    'type' => 'trait',
    'traitname' => 'Update',
    'namespace' => 'Stripe\\ApiOperations',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\ApiOperations\\Update',
    ),
  ),
  'Stripe\\Service\\ServiceNavigatorTrait' => 
  array (
    'type' => 'trait',
    'traitname' => 'ServiceNavigatorTrait',
    'namespace' => 'Stripe\\Service',
    'use' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\Service\\ServiceNavigatorTrait',
    ),
  ),
  'Dompdf\\Canvas' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Canvas',
    'namespace' => 'Dompdf',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Dompdf\\Canvas',
    ),
  ),
  'Svg\\Surface\\SurfaceInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'SurfaceInterface',
    'namespace' => 'Svg\\Surface',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Svg\\Surface\\SurfaceInterface',
    ),
  ),
  'Firebase\\JWT\\JWTExceptionWithPayloadInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'JWTExceptionWithPayloadInterface',
    'namespace' => 'Firebase\\JWT',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Firebase\\JWT\\JWTExceptionWithPayloadInterface',
    ),
  ),
  'Google\\Task\\Retryable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Retryable',
    'namespace' => 'Google\\Task',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Task\\Retryable',
    ),
  ),
  'Google_Task_Retryable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Google_Task_Retryable',
    'namespace' => '\\',
    'extends' => 
    array (
      0 => 'AmeliaVendor_Google_Task_Retryable',
    ),
  ),
  'Google\\Auth\\ExternalAccountCredentialSourceInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ExternalAccountCredentialSourceInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\ExternalAccountCredentialSourceInterface',
    ),
  ),
  'Google\\Auth\\FetchAuthTokenInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'FetchAuthTokenInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\FetchAuthTokenInterface',
    ),
  ),
  'Google\\Auth\\GetQuotaProjectInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'GetQuotaProjectInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\GetQuotaProjectInterface',
    ),
  ),
  'Google\\Auth\\GetUniverseDomainInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'GetUniverseDomainInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\GetUniverseDomainInterface',
    ),
  ),
  'Google\\Auth\\ProjectIdProviderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ProjectIdProviderInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\ProjectIdProviderInterface',
    ),
  ),
  'Google\\Auth\\SignBlobInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'SignBlobInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\SignBlobInterface',
    ),
  ),
  'Google\\Auth\\UpdateMetadataInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'UpdateMetadataInterface',
    'namespace' => 'Google\\Auth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Google\\Auth\\UpdateMetadataInterface',
    ),
  ),
  'Masterminds\\HTML5\\InstructionProcessor' => 
  array (
    'type' => 'interface',
    'interfacename' => 'InstructionProcessor',
    'namespace' => 'Masterminds\\HTML5',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Masterminds\\HTML5\\InstructionProcessor',
    ),
  ),
  'Masterminds\\HTML5\\Parser\\EventHandler' => 
  array (
    'type' => 'interface',
    'interfacename' => 'EventHandler',
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\EventHandler',
    ),
  ),
  'Masterminds\\HTML5\\Parser\\InputStream' => 
  array (
    'type' => 'interface',
    'interfacename' => 'InputStream',
    'namespace' => 'Masterminds\\HTML5\\Parser',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Masterminds\\HTML5\\Parser\\InputStream',
    ),
  ),
  'Masterminds\\HTML5\\Serializer\\RulesInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RulesInterface',
    'namespace' => 'Masterminds\\HTML5\\Serializer',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Masterminds\\HTML5\\Serializer\\RulesInterface',
    ),
  ),
  'Monolog\\Formatter\\FormatterInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'FormatterInterface',
    'namespace' => 'Monolog\\Formatter',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Formatter\\FormatterInterface',
    ),
  ),
  'Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ActivationStrategyInterface',
    'namespace' => 'Monolog\\Handler\\FingersCrossed',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\FingersCrossed\\ActivationStrategyInterface',
    ),
  ),
  'Monolog\\Handler\\FormattableHandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'FormattableHandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\FormattableHandlerInterface',
    ),
  ),
  'Monolog\\Handler\\HandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'HandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\HandlerInterface',
    ),
  ),
  'Monolog\\Handler\\ProcessableHandlerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ProcessableHandlerInterface',
    'namespace' => 'Monolog\\Handler',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Handler\\ProcessableHandlerInterface',
    ),
  ),
  'Monolog\\LogRecord' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LogRecord',
    'namespace' => 'Monolog',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\LogRecord',
    ),
  ),
  'Monolog\\Processor\\ProcessorInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ProcessorInterface',
    'namespace' => 'Monolog\\Processor',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\Processor\\ProcessorInterface',
    ),
  ),
  'Monolog\\ResettableInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ResettableInterface',
    'namespace' => 'Monolog',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Monolog\\ResettableInterface',
    ),
  ),
  'ParagonIE\\ConstantTime\\EncoderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'EncoderInterface',
    'namespace' => 'ParagonIE\\ConstantTime',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\ParagonIE\\ConstantTime\\EncoderInterface',
    ),
  ),
  'PHPMailer\\PHPMailer\\OAuthTokenProvider' => 
  array (
    'type' => 'interface',
    'interfacename' => 'OAuthTokenProvider',
    'namespace' => 'PHPMailer\\PHPMailer',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\PHPMailer\\PHPMailer\\OAuthTokenProvider',
    ),
  ),
  'phpseclib3\\Crypt\\Common\\PrivateKey' => 
  array (
    'type' => 'interface',
    'interfacename' => 'PrivateKey',
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\PrivateKey',
    ),
  ),
  'phpseclib3\\Crypt\\Common\\PublicKey' => 
  array (
    'type' => 'interface',
    'interfacename' => 'PublicKey',
    'namespace' => 'phpseclib3\\Crypt\\Common',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\phpseclib3\\Crypt\\Common\\PublicKey',
    ),
  ),
  'Psr\\Cache\\CacheException' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CacheException',
    'namespace' => 'Psr\\Cache',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Cache\\CacheException',
    ),
  ),
  'Psr\\Cache\\CacheItemInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CacheItemInterface',
    'namespace' => 'Psr\\Cache',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Cache\\CacheItemInterface',
    ),
  ),
  'Psr\\Cache\\CacheItemPoolInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CacheItemPoolInterface',
    'namespace' => 'Psr\\Cache',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Cache\\CacheItemPoolInterface',
    ),
  ),
  'Psr\\Cache\\InvalidArgumentException' => 
  array (
    'type' => 'interface',
    'interfacename' => 'InvalidArgumentException',
    'namespace' => 'Psr\\Cache',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Cache\\InvalidArgumentException',
    ),
  ),
  'Psr\\Http\\Client\\ClientExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ClientExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Http\\Client\\ClientExceptionInterface',
    ),
  ),
  'Psr\\Http\\Client\\ClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ClientInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Http\\Client\\ClientInterface',
    ),
  ),
  'Psr\\Http\\Client\\NetworkExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NetworkExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Http\\Client\\NetworkExceptionInterface',
    ),
  ),
  'Psr\\Http\\Client\\RequestExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RequestExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Http\\Client\\RequestExceptionInterface',
    ),
  ),
  'Psr\\Log\\LoggerAwareInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LoggerAwareInterface',
    'namespace' => 'Psr\\Log',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Log\\LoggerAwareInterface',
    ),
  ),
  'Psr\\Log\\LoggerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LoggerInterface',
    'namespace' => 'Psr\\Log',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Psr\\Log\\LoggerInterface',
    ),
  ),
  'WpOrg\\Requests\\Capability' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Capability',
    'namespace' => 'WpOrg\\Requests',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\WpOrg\\Requests\\Capability',
    ),
  ),
  'WpOrg\\Requests\\HookManager' => 
  array (
    'type' => 'interface',
    'interfacename' => 'HookManager',
    'namespace' => 'WpOrg\\Requests',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\WpOrg\\Requests\\HookManager',
    ),
  ),
  'Sabberworm\\CSS\\CSSElement' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CSSElement',
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\CSSElement',
    ),
  ),
  'Sabberworm\\CSS\\Comment\\Commentable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Commentable',
    'namespace' => 'Sabberworm\\CSS\\Comment',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\Comment\\Commentable',
    ),
  ),
  'Sabberworm\\CSS\\Position\\Positionable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Positionable',
    'namespace' => 'Sabberworm\\CSS\\Position',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\Position\\Positionable',
    ),
  ),
  'Sabberworm\\CSS\\Property\\AtRule' => 
  array (
    'type' => 'interface',
    'interfacename' => 'AtRule',
    'namespace' => 'Sabberworm\\CSS\\Property',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\Property\\AtRule',
    ),
  ),
  'Sabberworm\\CSS\\Renderable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Renderable',
    'namespace' => 'Sabberworm\\CSS',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabberworm\\CSS\\Renderable',
    ),
  ),
  'Sabre\\VObject\\Splitter\\SplitterInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'SplitterInterface',
    'namespace' => 'Sabre\\VObject\\Splitter',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\VObject\\Splitter\\SplitterInterface',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\TimezoneFinder' => 
  array (
    'type' => 'interface',
    'interfacename' => 'TimezoneFinder',
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\TimezoneFinder',
    ),
  ),
  'Sabre\\VObject\\TimezoneGuesser\\TimezoneGuesser' => 
  array (
    'type' => 'interface',
    'interfacename' => 'TimezoneGuesser',
    'namespace' => 'Sabre\\VObject\\TimezoneGuesser',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\VObject\\TimezoneGuesser\\TimezoneGuesser',
    ),
  ),
  'Sabre\\Xml\\XmlDeserializable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'XmlDeserializable',
    'namespace' => 'Sabre\\Xml',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\Xml\\XmlDeserializable',
    ),
  ),
  'Sabre\\Xml\\XmlSerializable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'XmlSerializable',
    'namespace' => 'Sabre\\Xml',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Sabre\\Xml\\XmlSerializable',
    ),
  ),
  'Stripe\\BaseStripeClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'BaseStripeClientInterface',
    'namespace' => 'Stripe',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\BaseStripeClientInterface',
    ),
  ),
  'Stripe\\Exception\\ExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ExceptionInterface',
    'namespace' => 'Stripe\\Exception',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\Exception\\ExceptionInterface',
    ),
  ),
  'Stripe\\Exception\\OAuth\\ExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ExceptionInterface',
    'namespace' => 'Stripe\\Exception\\OAuth',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\Exception\\OAuth\\ExceptionInterface',
    ),
  ),
  'Stripe\\HttpClient\\ClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ClientInterface',
    'namespace' => 'Stripe\\HttpClient',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\HttpClient\\ClientInterface',
    ),
  ),
  'Stripe\\HttpClient\\StreamingClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'StreamingClientInterface',
    'namespace' => 'Stripe\\HttpClient',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\HttpClient\\StreamingClientInterface',
    ),
  ),
  'Stripe\\StripeClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'StripeClientInterface',
    'namespace' => 'Stripe',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\StripeClientInterface',
    ),
  ),
  'Stripe\\StripeStreamingClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'StripeStreamingClientInterface',
    'namespace' => 'Stripe',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\StripeStreamingClientInterface',
    ),
  ),
  'Stripe\\Util\\LoggerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'LoggerInterface',
    'namespace' => 'Stripe\\Util',
    'extends' => 
    array (
      0 => 'AmeliaVendor\\Stripe\\Util\\LoggerInterface',
    ),
  ),
);

        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }

        /**
         * @param string $class
         */
        public function autoload($class): void
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                        $this->load(
                            $this->classTemplate(
                                $this->autoloadAliases[$class]
                            )
                        );
                    break;
                case 'interface':
                    $this->load(
                        $this->interfaceTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                case 'trait':
                    $this->load(
                        $this->traitTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                default:
                    // Never.
                    break;
            }
        }

        private function load(string $includeFile): void
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }

        /**
         * @param ClassAliasArray $class
         */
        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? ''
                : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? ''
                : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
                <?php
                $namespace
                $abstract class $classname extends $extends $implements {}
                EOD;
        }

        /**
         * @param InterfaceAliasArray $interface
         */
        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace'])
            ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace'])
            ? '\\' . implode('\\ ,', $interface['extends'])
            : implode(', ', $interface['extends']);
            return <<<EOD
                <?php
                $namespace
                interface $interfacename extends $extends {}
                EOD;
        }

        /**
         * @param TraitAliasArray $trait
         */
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace'])
            ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace'])
            ? '\\' . implode(';' . PHP_EOL . '    use \\', $trait['use'])
            : implode(';' . PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
                <?php
                $namespace
                trait $traitname { 
                    use $uses; 
                }
                EOD;
        }
    }

    spl_autoload_register([ new AliasAutoloader(), 'autoload' ]);
}

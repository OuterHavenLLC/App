<?php
 class DOMDocument extends DOMNode implements DOMParentNode {
  public readonly ?DOMDocumentType $doctype;
  public readonly DOMImplementation $implementation;
  public readonly ?DOMElement $documentElement;
  public readonly ?string $actualEncoding;
  public ?string $encoding;
  public readonly ?string $xmlEncoding;
  public bool $standalone;
  public bool $xmlStandalone;
  public ?string $version;
  public ?string $xmlVersion;
  public bool $strictErrorChecking;
  public ?string $documentURI;
  public readonly mixed $config;
  public bool $formatOutput;
  public bool $validateOnParse;
  public bool $resolveExternals;
  public bool $preserveWhiteSpace;
  public bool $recover;
  public bool $substituteEntities;
  public readonly ?DOMElement $firstElementChild;
  public readonly ?DOMElement $lastElementChild;
  public readonly int $childElementCount;
  public readonly string $nodeName;
  public ?string $nodeValue;
  public readonly int $nodeType;
  public readonly ?DOMNode $parentNode;
  public readonly ?DOMElement $parentElement;
  public readonly DOMNodeList $childNodes;
  public readonly ?DOMNode $firstChild;
  public readonly ?DOMNode $lastChild;
  public readonly ?DOMNode $previousSibling;
  public readonly ?DOMNode $nextSibling;
  public readonly ?DOMNamedNodeMap $attributes;
  public readonly bool $isConnected;
  public readonly ?DOMDocument $ownerDocument;
  public readonly ?string $namespaceURI;
  public string $prefix;
  public readonly ?string $localName;
  public readonly ?string $baseURI;
  public string $textContent;
  public __construct(string $version = "1.0", string $encoding = "")
  public adoptNode(DOMNode $node): DOMNode|false
  public append(DOMNode|string ...$nodes): void
  public createAttribute(string $localName): DOMAttr|false
  public createAttributeNS(?string $namespace, string $qualifiedName): DOMAttr|false
  public createCDATASection(string $data): DOMCdataSection|false
  public createComment(string $data): DOMComment
  public createDocumentFragment(): DOMDocumentFragment
  public createElement(string $localName, string $value = ""): DOMElement|false
  public createElementNS(?string $namespace, string $qualifiedName, string $value = ""): DOMElement|false
  public createEntityReference(string $name): DOMEntityReference|false
  public createProcessingInstruction(string $target, string $data = ""): DOMProcessingInstruction|false
  public createTextNode(string $data): DOMText
  public getElementById(string $elementId): ?DOMElement
  public getElementsByTagName(string $qualifiedName): DOMNodeList
  public getElementsByTagNameNS(?string $namespace, string $localName): DOMNodeList
  public importNode(DOMNode $node, bool $deep = false): DOMNode|false
  public load(string $filename, int $options = 0): bool
  public loadHTML(string $source, int $options = 0): bool
  public loadHTMLFile(string $filename, int $options = 0): bool
  public loadXML(string $source, int $options = 0): bool
  public normalizeDocument(): void
  public prepend(DOMNode|string ...$nodes): void
  public registerNodeClass(string $baseClass, ?string $extendedClass): bool
  public relaxNGValidate(string $filename): bool
  public relaxNGValidateSource(string $source): bool
  public replaceChildren(DOMNode|string ...$nodes): void
  public save(string $filename, int $options = 0): int|false
  public saveHTML(?DOMNode $node = null): string|false
  public saveHTMLFile(string $filename): int|false
  public saveXML(?DOMNode $node = null, int $options = 0): string|false
  public schemaValidate(string $filename, int $flags = 0): bool
  public schemaValidateSource(string $source, int $flags = 0): bool
  public validate(): bool
  public xinclude(int $options = 0): int|false
  public DOMNode::appendChild(DOMNode $node): DOMNode|false
  public DOMNode::C14N(
   bool $exclusive = false,
   bool $withComments = false,
   ?array $xpath = null,
   ?array $nsPrefixes = null
  ): string|false
  public DOMNode::C14NFile(
   string $uri,
   bool $exclusive = false,
   bool $withComments = false,
   ?array $xpath = null,
   ?array $nsPrefixes = null
  ): int|false
  public DOMNode::cloneNode(bool $deep = false): DOMNode|false
  public DOMNode::contains(DOMNode|DOMNameSpaceNode|null $other): bool
  public DOMNode::getLineNo(): int
  public DOMNode::getNodePath(): ?string
  public DOMNode::getRootNode(array $options = null): DOMNode
  public DOMNode::hasAttributes(): bool
  public DOMNode::hasChildNodes(): bool
  public DOMNode::insertBefore(DOMNode $node, ?DOMNode $child = null): DOMNode|false
  public DOMNode::isDefaultNamespace(string $namespace): bool
  public DOMNode::isEqualNode(?DOMNode $otherNode): bool
  public DOMNode::isSameNode(DOMNode $otherNode): bool
  public DOMNode::isSupported(string $feature, string $version): bool
  public DOMNode::lookupNamespaceURI(?string $prefix): ?string
  public DOMNode::lookupPrefix(string $namespace): ?string
  public DOMNode::normalize(): void
  public DOMNode::removeChild(DOMNode $child): DOMNode|false
  public DOMNode::replaceChild(DOMNode $node, DOMNode $child): DOMNode|false
 }
?>
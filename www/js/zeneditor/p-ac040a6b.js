/*!-----------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Version: 0.37.1(20a8d5a651d057aaed7875ad1c1f2ecf13c4e773)
 * Released under the MIT license
 * https://github.com/microsoft/monaco-editor/blob/main/LICENSE.txt
 *-----------------------------------------------------------------------------*/
var e=e=>`\\b${e}\\b`;var n="[_a-zA-Z]";var t="[_a-zA-Z0-9]";var o=e(`${n}${t}*`);var r=["targetScope","resource","module","param","var","output","for","in","if","existing"];var i=["true","false","null"];var a=`[ \\t\\r\\n]`;var s=`[0-9]+`;var c={comments:{lineComment:"//",blockComment:["/*","*/"]},brackets:[["{","}"],["[","]"],["(",")"]],surroundingPairs:[{open:"{",close:"}"},{open:"[",close:"]"},{open:"(",close:")"},{open:"'",close:"'"},{open:"'''",close:"'''"}],autoClosingPairs:[{open:"{",close:"}"},{open:"[",close:"]"},{open:"(",close:")"},{open:"'",close:"'",notIn:["string","comment"]},{open:"'''",close:"'''",notIn:["string","comment"]}],autoCloseBefore:":.,=}])' \n\t",indentationRules:{increaseIndentPattern:new RegExp("^((?!\\/\\/).)*(\\{[^}\"'`]*|\\([^)\"'`]*|\\[[^\\]\"'`]*)$"),decreaseIndentPattern:new RegExp("^((?!.*?\\/\\*).*\\*/)?\\s*[\\}\\]].*$")}};var g={defaultToken:"",tokenPostfix:".bicep",brackets:[{open:"{",close:"}",token:"delimiter.curly"},{open:"[",close:"]",token:"delimiter.square"},{open:"(",close:")",token:"delimiter.parenthesis"}],symbols:/[=><!~?:&|+\-*/^%]+/,keywords:r,namedLiterals:i,escapes:`\\\\(u{[0-9A-Fa-f]+}|n|r|t|\\\\|'|\\\${)`,tokenizer:{root:[{include:"@expression"},{include:"@whitespace"}],stringVerbatim:[{regex:`(|'|'')[^']`,action:{token:"string"}},{regex:`'''`,action:{token:"string.quote",next:"@pop"}}],stringLiteral:[{regex:`\\\${`,action:{token:"delimiter.bracket",next:"@bracketCounting"}},{regex:`[^\\\\'$]+`,action:{token:"string"}},{regex:"@escapes",action:{token:"string.escape"}},{regex:`\\\\.`,action:{token:"string.escape.invalid"}},{regex:`'`,action:{token:"string",next:"@pop"}}],bracketCounting:[{regex:`{`,action:{token:"delimiter.bracket",next:"@bracketCounting"}},{regex:`}`,action:{token:"delimiter.bracket",next:"@pop"}},{include:"expression"}],comment:[{regex:`[^\\*]+`,action:{token:"comment"}},{regex:`\\*\\/`,action:{token:"comment",next:"@pop"}},{regex:`[\\/*]`,action:{token:"comment"}}],whitespace:[{regex:a},{regex:`\\/\\*`,action:{token:"comment",next:"@comment"}},{regex:`\\/\\/.*$`,action:{token:"comment"}}],expression:[{regex:`'''`,action:{token:"string.quote",next:"@stringVerbatim"}},{regex:`'`,action:{token:"string.quote",next:"@stringLiteral"}},{regex:s,action:{token:"number"}},{regex:o,action:{cases:{"@keywords":{token:"keyword"},"@namedLiterals":{token:"keyword"},"@default":{token:"identifier"}}}}]}};export{c as conf,g as language};
//# sourceMappingURL=p-ac040a6b.js.map
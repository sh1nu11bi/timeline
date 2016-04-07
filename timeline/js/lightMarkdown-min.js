/**
 * Created by ShaLi on 07/01/2016.
 */
"use strict"
function escapeHtml(e){return e&&(e=e.replace(/>/g,"&gt;"),e=e.replace(/</g,"&lt;")),e}function getRegex(e){var n=e.map(function(e){return 1===e.token.length?e.token:""}).join(),t=new RegExp("[^"+n+"]"),o=/(^|\n)&gt;&gt;&gt;([\s\S]*$)/,r=/(^|\n)&gt;(([^\n]*)(\n&gt;[^\n]*)*)/g,a=/\r?\n\r?\n\r?/g,i=/\r?\n\r?/g,s=/(^|\s)((?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,}))\.?)(?::\d{2,5})?(?:[\/?#]\S*)?)/gi
return{nonTokensChars:t,multilineQuote:o,singleLineQuote:r,blockquoteTags:/<\/?blockquote>/gi,doubleLineBreak:a,singleLineBreak:i,url:s}}function escapeRegExp(e){return e.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")}function getTokens(){var e=[{name:"pre",token:"```",elementName:"pre",multiline:!0,plainContent:!0},{name:"code",token:"`",elementName:"code",ignoreAfter:!0,plainContent:!0},{name:"bold",token:"*",elementName:"b",requireNonTokens:!0,spaceWrapIgnored:!0},{name:"italics",token:"_",elementName:"i",requireNonTokens:!0},{name:"strikethrough",token:"~",elementName:"s",requireNonTokens:!0,spaceWrapIgnored:!0}]
return e.forEach(function(e){if(!e.regex){var n='(^|[\\s\\?\\.,\\-!\\^;:{(\\[%$#+="])',t=e.multiline?"([\\s\\S]*?)?":"(.*?\\S *)?",o=e.ignoreAfter?"":"(?=$|\\s|[\\?\\.,'\\-!\\^;:})\\]%$~{\\[<#+=\"])",r=escapeRegExp(e.token),a=n+r+t+r+o
e.regex=new RegExp(a,"g")}}),e}var lightMarkdown={},tokens=getTokens(),regex=getRegex(tokens),plainToken="₪₪PLaiN₪₪",optionsMarkDown={},flavors={slack:{bold:!0,italics:!0,strikethrough:!0,pre:!0,code:!0,longQuote:!0,quote:!0,autoLink:!0,paragraph:!0,lineBreaks:!0},skype:{bold:!0,italics:!0,strikethrough:!0,pre:!1,code:!1,longQuote:!1,quote:!1,autoLink:!0,paragraph:!1,lineBreaks:!0}}
lightMarkdown.setOption=function(e,n){return optionsMarkDown[e]=!!n,this},lightMarkdown.getOption=function(e){return optionsMarkDown[e]},lightMarkdown.setFlavor=function(e){var n=flavors[e]
if(n)for(var t in n)n.hasOwnProperty(t)&&(optionsMarkDown[t]=n[t])
return this},lightMarkdown.toHtml=function(e){e=escapeHtml(e)
var n=[]
if(tokens.forEach(function(t){optionsMarkDown[t.name]&&(e=e.replace(t.regex,function(e,o,r){if(!r||t.requireNonTokens&&!regex.nonTokensChars.test(r)||1===t.token.length&&(r[0]===t.token||r.slice(-1)===t.token)||t.spaceWrapIgnored&&" "===r[0]&&" "===r.slice(-1))return e
if("function"==typeof t.processContent&&(r=t.processContent(r)),t.plainContent){var a=n.push(r)-1
r=plainToken+a}return o+"<"+t.elementName+">"+r+"</"+t.elementName+">"}))}),optionsMarkDown.longQuote&&(e=e.replace(regex.multilineQuote,function(e,n,t){return"&gt;&gt;&gt;"===e?e:(t=t.replace(/^([\s]*)(&gt;)*/,function(e,n,t){return t?e:""}),"<blockquote>"+t+"</blockquote>")})),optionsMarkDown.quote&&(e=e.replace(regex.singleLineQuote,function(e,n,t){return"&gt;"===e?e:(t=t.replace(/\n&gt;/g,"\n"),"<blockquote>"+t+"</blockquote>")})),optionsMarkDown.autoLink&&(e=e.replace(regex.url,function(e,n,t){return n+'<a href="'+t+'" target="_blank">'+t+"</a>"})),optionsMarkDown.paragraph){for(var t,o=[];t=regex.doubleLineBreak.exec(e);)o.push({start:t.index,length:t[0].length})
for(;t=regex.blockquoteTags.exec(e);)o.push({start:t.index,length:t[0].length,suffix:t[0]})
o.push({start:e.length,length:0}),o.sort(function(e,n){return e.start-n.start})
var r="",a=0
o.forEach(function(n){var t="",o=e.substring(a,n.start)
o&&(t="<p>"+o+"</p>"),n.suffix&&(t+=n.suffix),r+=t,a=n.start+n.length}),e=r}return optionsMarkDown.lineBreaks&&(e=e.replace(regex.singleLineBreak,"<br/>")),n.forEach(function(n,t){e=e.replace(plainToken+t,n)}),e},lightMarkdown.setFlavor("slack")

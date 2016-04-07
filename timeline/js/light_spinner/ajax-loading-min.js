/**
 * Created by toplan on 15/7/11.
 */
!function(i){function n(n){var o="display: none;position: fixed;top: 0;bottom: 0;left: 0;right: 0;margin: auto;padding: 8px;text-align: center;vertical-align: middle;",d=["width:"+n.width,"height:"+n.height,"z-index:"+n.zIndex,"background:"+n.background,"border-radius:"+n.radius]
o+=d.join(";")
var a="margin-bottom:8px;"
d=["width:"+n.imgWidth,"height:"+n.imgWidth],a+=d.join(";")
var t="margin:0;"
d=["font-size:"+n.fontSize,"color:"+n.fontColor],t+=d.join(";")
var e='<div id="'+n.id+'" style="'+o+'"><img src="'+n.imgPath+'" style="'+a+'"><p style="'+t+'">'+n.tip+"</p></div>",e=' <div id="'+n.id+'" style="'+o+'">                         <div class="cssload-container">                             <div class="cssload-whirlpool"></div>                         </div>                     </div>'
i(document).find("body").append(e)}var o={}
i.loading=function(d){var a=i.extend(i.loading["default"],d)
o=a,n(a)
var t,e="#"+a.id
return i(document).on("ajaxStart",function(){o.ajax&&(t=setTimeout(function(){i(e).show()},400))}),i(document).on("ajaxComplete",function(){clearTimeout(t),i(e).hide()}),i.loading},i.loading.open=function(n){var d="#"+o.id
i(d).hide()},i.loading.close=function(){var n="#"+o.id
i(n).hide()},i.loading.ajax=function(i){o.ajax=i},i.loading["default"]={ajax:!0,id:"ajaxLoading",zIndex:"1000",background:"rgba(0, 0, 0, 0)",minTime:500,radius:"4px",width:"85px",height:"85px",imgPath:"img/ajax-loading.gif",imgWidth:"45px",imgHeight:"45px",tip:"loading...",fontSize:"14px",fontColor:"#fff"}}(window.jQuery||window.Zepto)

// helper function
//we ask for 14 days 
// we load the spinner jquery plugin and config
//start date
// user preferences 
// integrations to show
// all integrations to work
//we get integrations selected config first and we get the type also
//we make the Dataset instance for later update it
//basic options config for timeline
function getStorage(e){try{data_string=localStorage.getItem(e)
var t=JSON.parse(data_string)}catch(n){var t=null}return t}function getNextDate(e,t){var n=new Date(e)
return n.setDate(e.getDate()+t),n}function getUrlParams(){var e=function(e){if(""==e)return{}
for(var t={},n=0;n<e.length;++n){var a=e[n].split("=",2)
1==a.length?t[a[0]]="":t[a[0]]=decodeURIComponent(a[1].replace(/\+/g," "))}return t}(window.location.search.substr(1).split("&"))
return e}function getParams(e){var t={}
t.integrations=[]
for(var n in e)n.beginsWith("Integrations")&&t.integrations.push(e[n])
return t.start=e.start,t.end=e.end,t}function getUserPreferences(e){dashboard_preferences=getStorage("dashboard_preferences"),null===dashboard_preferences&&(dashboard_preferences={},dashboard_preferences.integrations=[],index_counter=0,$.each(e,function(){return dashboard_preferences.integrations.push({Integration_Name:this.integration_name,API_key:this.apiKey,Owner:this.user,Created_On:this.createdOn}),2==index_counter?!1:void index_counter++}))
var t=getUrlParams()
if(selected_integrations=Array(),0!=Object.keys(t)){var n=getParams(t)
for(i=0;i<e.length;i++)n.integrations.indexOf(e[i].integration_name)>-1&&selected_integrations.push({Integration_Name:e[i].integration_name,API_key:e[i].apiKey,Owner:e[i].user,Created_On:e[i].createdOn,type:e[i].type})
return param_start=new Date(parseInt(n.start)),param_end=new Date(parseInt(n.end)),selected_integrations}for(selected_integrations=dashboard_preferences.integrations,i=0;i<e.length;i++)for(x=0;x<selected_integrations.length;x++)e[i].integration_name==selected_integrations[x].Integration_Name&&(selected_integrations[x].type=e[i].type)}function generateGroups(){if(set_groups===!1)for(var e=0;e<selected_integrations.length;e++){var t=selected_integrations[e]
groups.add({id:e,content:t.Integration_Name}),e==selected_integrations.length-1&&(set_groups=!0)}}function generateContainer(){set_container===!1&&(container=document.getElementById("visualization"),usable_height=$(window).height()-$("#menunav").outerHeight()-parseInt($("body").css("margin-bottom")),usable_height-=.1*usable_height,options.minHeight=usable_height,options.maxHeight=usable_height,timeline=new vis.Timeline(container),timeline.setOptions(options),timeline.setGroups(groups),set_container=!0)}function checkTooltips(){data_elements=$("[data-json_string]"),data_elements.length>0&&$("[data-json_string]").qtip({content:{attr:"data-json_string"},position:{my:"bottom center",at:"top center"},style:{classes:"qtip-jtools nice_json"}})}function getHumanDate(e){var t=new moment(e),n=t.toString().split(" ")
if(n.length>=6){var a=n.slice(0,5).join(" ")
return a}return console.log("Something bad with the date!"),""}function getShareLink(){var e={}
for(x=0;x<selected_integrations.length;x++)e["Integrations"+x.toString()]=selected_integrations[x].Integration_Name
current_range=timeline.getWindow(),e.start=current_range.start.getTime(),e.end=current_range.end.getTime()
var t=$.param(e),n=window.location.protocol+"//"+window.location.hostname+"/dashboard?"+t
return n}function fix404Images(){var e=document.getElementsByTagName("img")
for(i=0;i<e.length;i++)e[i].onerror=e[i].src="/images/webhook_custom.png"}function addNewRelicItem(e,t){var n=e.alert_url.split("/"),a=n[n.length-1],s='<a target="_blank" href="'+e.alert_url+'">'+a+"</a>",o='<img  src="/images/NewRelic-logo-square_small.png"  width="20" height="20" onerror="this.src=\'/images/webhook_custom.png\';">'+s,r=e.created_at,i='<div class="tooltip-title">'+e.short_description+"</div>",l="<p>"+e.short_description+"</p>",g='<p><span class="json-key tooltip-label">TYPE:</span>'+e.severity+"</p>",c='<p><span class="json-key tooltip-label">APP NAME: </span>'+e.application_name+"</p>",d='<p><span class="json-key tooltip-label">MESSAGE: </span>'+e.message+"</p>",p=i+l+g+c+d
newRelic_statuses={OPEN:"red",ACKNOWLEDGED:"orange",CLOSE:"green"}
var u={id:e._id,group:t,content:o,start:r,type:"box",className:newRelic_statuses[e.current_state],json_string:p}
items.update(u)}function addPagerdutyItem(e,t){var n='<a target="_blank" href="'+e.html_url+'">'+e.id+"</a>",a='<img  src="/images/pdlogo.png" width="20" height="20" onerror="this.src=\'/images/webhook_custom.png\';">'+n,s=e.id,o=e.created_on
if(void 0!==e.trigger_summary_data.subject)var r='<div class="tooltip-title">'+e.trigger_summary_data.subject+"</div>"
else var i=e.trigger_summary_data.HOSTNAME+": "+e.trigger_summary_data.SERVICEDESC+" "+e.trigger_summary_data.HOSTSTATE,r='<div class="tooltip-title">'+i+"</div>"
var l='<p><span class="json-key tooltip-label">EVENT ID: </span>'+e.id+"</p>",g='<p><span class="json-key tooltip-label">STATUS: </span>'+e.status+"</p>",c="<p></p>"
if("resolved"==e.status)if(void 0==e.resolved_by_user)var d='<p><span class="json-key tooltip-label">RESOLVED BY: </span>API</p>'
else if(null==e.resolved_by_user.name)var d='<p><span class="json-key tooltip-label">RESOLVED BY: </span> API </p>'
else var d='<p><span class="json-key tooltip-label">RESOLVED BY: </span>'+e.resolved_by_user.name+"</p>"
else if(null==e.assigned_to_user.name)var d='<p><span class="json-key tooltip-label">ASSIGNED TO: </span> none </p>'
else var d='<p><span class="json-key tooltip-label">ASSIGNED TO: </span>'+e.assigned_to_user.name+"</p>"
var p='<p><span class="json-key tooltip-label">CREATED ON: </span>'+getHumanDate(o)+"</p>",u=r+c+l+g+d+p
pagerduty_statuses={triggered:"red",acknowledged:"orange",resolved:"green"},g=pagerduty_statuses[e.status]
var m={id:s,group:t,content:a,start:o,end:e.last_status_change_on,type:"range",className:g,json_string:u}
"resolved"!==e.status&&(m.type="box",delete m.end),items.update(m)}function addCustomItem(e,t){var n='<img  src="/images/users_photos/'+e.username+'.jpg" width="30" height="30" hspace="3" onerror="this.src=\'/images/webhook_custom.png\';">'+e.username,a=e._id
if(void 0==e.start)var s=e.start_date
else var s=e.start
var o='<p><span class="json-key tooltip-label">USERNAME: </span>'+e.username+"</p>",r=lightMarkdown.toHtml(e.message).replace("<p>","").replace("</p>",""),i=emojify.replace(r)
i=i.replace("&amp;lt;","<").replace("&amp;gt;",">"),i=i.replace("&lt;","<").replace("&gt;",">")
var l='<p><span class="json-key tooltip-label">MESSAGE: </span>'+i+"</p>",g='<p><span class="json-key tooltip-label">CREATED ON: </span>'+getHumanDate(s)+"</p>",c=o+l+g
item_data={id:a,group:t,content:n,start:s,type:"box",json_string:c},items.update(item_data)}function reduceRange(){options.zoomMax>3024e5&&(options.zoomMax=Math.floor(options.zoomMax/2),timeline.setOptions(options))
var e=new vis.DataSet
$.each(items.getIds(),function(t,n){0===t&&(item=items.get(n),reduced_start=new Date(item.start)),599>t&&(item=items.get(n),e.add(item)),599===t&&(item=items.get(n),reduced_end=new Date(item.start))}),e.length>0&&(items=e,start=reduced_start,end=reduced_end)}function getItemsRange(e){e.end>getNextDate(end,1)?(items.clear(),start=getNextDate(e.start,-1),end=getNextDate(e.start,days_interval),console.log([e.start,e.end]),console.log(["generateDashboard right",start,end]),generateDashboard(start,end)):e.start<getNextDate(start,-1)&&(items.clear(),end=getNextDate(e.end,1),start=getNextDate(e.end,-days_interval),console.log([e.start,e.end]),console.log(["generateDashboard left",start,end]),generateDashboard(start,end)),checkTooltips()}function generateDashboard(e,t,n){generateGroups(),generateContainer()
for(var a=0;a<selected_integrations.length;a++){var s=selected_integrations[a]
if("undefined"==typeof n||n===s.Integration_Name){var o=url_start+"/webhooks/integrations/"+s.Integration_Name+"/search",r={search:"",type:s.type,start_date:e.toISOString(),end_date:t.toISOString()}
$.ajax({type:"POST",url:o,contentType:"application/json",data:JSON.stringify(r),custom_group:a,selected_integration:s,success:function(n){integration_list[s.Integration_Name].last_update=new Date
for(var a=0;a<=n.length-1;a++){event_data=n[a]
try{switch(this.selected_integration.type){case"newrelic":addNewRelicItem(event_data,this.custom_group)
break
case"pagerduty":addPagerdutyItem(event_data,this.custom_group)
break
case"custom":addCustomItem(event_data,this.custom_group)}}catch(o){console.log("Error",o.stack),console.log("Error",o.name),console.log("Error",o.message)}}this.custom_group==selected_integrations.length-1?(0==set_event&&(timeline.off("rangechanged",getItemsRange),timeline.on("rangechanged",getItemsRange),set_event=!0),timeline.setItems(items),checkTooltips(),param_start!==!1&&(timeline.setWindow(e,t),param_start=!1)):timeline.setItems(items)},error:function(e,t,n){alert(n.Message)},dataType:"json"})}}}function setIntegrationList(){for(i=0;i<selected_integrations.length;i++){var e={last_update:new Date,integration_group:i}
integration_list[selected_integrations[i].Integration_Name]=e}}function zoom(e){var t=timeline.getWindow(),n=t.end-t.start
timeline.setWindow({start:t.start.valueOf()-n*e,end:t.end.valueOf()+n*e,animation:!1})}function move(e){var t=timeline.getWindow(),n=t.end-t.start
timeline.setWindow({start:t.start.valueOf()-n*e,end:t.end.valueOf()-n*e,animation:!1})}String.prototype.beginsWith=function(e){return 0===this.indexOf(e)},url_start=window.location.protocol+"//"+window.location.hostname+"/api/",days_interval=20,loading=$.loading(),loader_config={id:"visualization"},param_start=!1,dashboard_preferences={},selected_integrations={},integration_list={},$.ajax({url:window.location.protocol+"//"+window.location.hostname+"/api/integrations/getAll",contentType:"application/json",async:!1,success:function(e){getUserPreferences(e)},error:function(e,t,n){alert("Error Getting preferences..")},dataType:"json"}),items=new vis.DataSet,groups=new vis.DataSet,set_groups=!1,set_container=!1,set_event=!1,"undefined"==typeof start&&(end=new Date,start=getNextDate(end,-days_interval)),options={groupOrder:"content",editable:!1,groupEditable:!1,width:"100%",margin:{item:10},minHeight:"",maxHeight:"",zoomMin:1e6,zoomMax:12096e5,dataAttributes:"all"},setIntegrationList(),param_start!==!1&&(start=param_start,end=param_end),generateDashboard(start,end),$(document).ready(function(){$("#zoomIn").mousedown(function(){interval=setInterval(function(){zoom(-.03)},35)}),$("#zoomIn").mouseup(function(){clearInterval(interval)}),$("#zoomOut").mousedown(function(){interval=setInterval(function(){zoom(.03)},35)}),$("#zoomOut").mouseup(function(){clearInterval(interval)}),$("#moveLeft").mousedown(function(){interval=setInterval(function(){move(.03)},35)}),$("#moveLeft").mouseup(function(){clearInterval(interval)}),$("#moveRight").mousedown(function(){interval=setInterval(function(){move(-.03)},35)}),$("#moveRight").mouseup(function(){clearInterval(interval)}),$("#button1").click(function(){var e=timeline.getCurrentTime()
e.setHours(e.getHours()+1)
var t=new Date(e)
t.setHours(e.getHours()-6),timeline.setWindow(t,e)}),$("#share").click(function(){var e=getShareLink()
$("#linktext").empty(),$("#linktext").append(e),$("#linktext").on("mouseup",function(){$(this)[0].select()})
var t=new Clipboard("#copyToClipboard")
t.on("success",function(e){$("#linktext").qtip({content:{text:"Link Copied!",show:!0},position:{my:"bottom right",at:"top center"},style:{classes:"qtip-jtools nice_json"}}),$("#linktext").qtip("toggle",!0)}),t.on("error",function(e){$("#linktext").qtip({content:{text:"⌘ + C",show:!0},position:{my:"bottom right",at:"top center"},style:{classes:"qtip-jtools nice_json"}}),$("#linktext").qtip("toggle",!0)})}),setInterval(function(){$.ajax({url:window.location.protocol+"//"+window.location.hostname+"/event_logs/all_logs.log",success:function(e){var t=JSON.parse(e)
for(x=0;x<t.length;x++){var n=t[x]
if(n&&n.integration_name in integration_list){var a=integration_list[n.integration_name]
switch(n.integration_type){case"pagerduty":addPagerdutyItem(n.json_data,a.integration_group)
break
case"newrelic":addNewRelicItem(n.json_data,a.integration_group)
break
case"custom":addCustomItem(n.json_data,a.integration_group)}}}},error:function(e,t,n){console.log(err.Message),console.log(e.responseText),console.log(t)}})},1e4)})

// helper function
String.prototype.beginsWith = function (string) {
    return(this.indexOf(string) === 0);
};

url_start = window.location.protocol + '//' + window.location.hostname + '/api/';

//we ask for 14 days 
days_interval = 14;

// we load the spinner jquery plugin and config
loading = $.loading();
loader_config = {id: 'visualization'};

//start date
param_start = false;

// user preferences 
dashboard_preferences = {};

// integrations to show
selected_integrations = {};

// all integrations to work
integration_list = {};


/**
* Get config data by key 
*
* @key from local storage
* @return JSON value or null
*/
function getStorage(key)
{        
    try {
        data_string = localStorage.getItem(key);
        var config_data = JSON.parse(data_string);
    }
    catch(err) {
        var config_data = null;            
    }
    return(config_data);
}

/**
* Get the next value from the date using
* using the interval
*
* @from is the date start to calculate from in days number
* @return Date
*/
function getNextDate(from, interval)
{             
    var end = new Date(from);
    end.setDate(from.getDate() + interval);
    return end;
}

/**
* Get the url parameters for the share link 
*    
*/
function getUrlParams()
{
    var qs = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=', 2);
            if (p.length == 1)
                b[p[0]] = "";
            else
                b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'));        

    return qs;
}

/**
* Get the config parameters to pass to timeline
*    
* @qs decoded url parameters
* @return object with config
*/   
function getParams(qs)
{
    var paramsData = {};
    paramsData.integrations = [];

    for (var key in qs) 
    {
        if (key.beginsWith('Integrations'))
        {
            paramsData.integrations.push(qs[key]);
        }
    }

    paramsData.start = qs.start;
    paramsData.end = qs.end;

    return paramsData;
}





/**
* Try to get the user preferences from local storage
*    
* @data is all the integrations data from API
* @return object with config
*/ 
function getUserPreferences(data)
{
    dashboard_preferences = getStorage('dashboard_preferences');
    //we check if there is no integration selected by the user
    if (dashboard_preferences === null)
    {
        dashboard_preferences = {};
        dashboard_preferences.integrations = [];        
        index_counter = 0;
        $.each(data, function() {                                
                                      
            dashboard_preferences.integrations.push({
                Integration_Name: this.integration_name,
                API_key: this.apiKey,
                Owner: this.user,
                Created_On: this.createdOn
            });
            if (index_counter == 2)            
            {
                return false;
            }
            index_counter++;
        });
    }

    //first we check if we have url params from share button
    var qs = getUrlParams(); 

    selected_integrations = Array();       

    if (Object.keys(qs) != 0)
    {
        var params = getParams(qs);
        for (i=0; i<data.length; i++)
        {
            if (params.integrations.indexOf(data[i].integration_name) > -1)               
            {
                selected_integrations.push({
                    Integration_Name: data[i].integration_name,
                    API_key: data[i].apiKey,
                    Owner: data[i].user,
                    Created_On: data[i].createdOn,
                    type: data[i].type
                })
            }                    
        }

        param_start = new Date(parseInt(params.start));                        
        param_end = new Date(parseInt(params.end));

        return selected_integrations;
    }
    else
    {
        selected_integrations = dashboard_preferences.integrations;    
        for (i=0; i<data.length; i++)
        {
            for (x=0; x<selected_integrations.length; x++ )
            {
                if (data[i].integration_name == selected_integrations[x].Integration_Name)
                {
                    selected_integrations[x].type = data[i].type;
                }
            }
        }
    }                        

    
}


//we get integrations selected config first and we get the type also
$.ajax({        
    url: window.location.protocol + "//" + window.location.hostname + "/api/integrations/getAll",
    contentType: "application/json", 
    async:false, 
    success: function(response_data) {
        getUserPreferences(response_data);
    },
    error: function(xhr, status, error) {                
        alert('Error Getting preferences..');
    },        
    dataType: 'json'        
});



/**
* Add the groups if needed from selected integrations
* to timeline       
*/ 
function generateGroups()
{
    if (set_groups === false)        
    {
        for (var g = 0; g < selected_integrations.length; g++) 
        {                                
            var sel_integration = selected_integrations[g];

            groups.add({id: g, content: sel_integration.Integration_Name});    
            if (g == (selected_integrations.length - 1)) 
            {                
                set_groups = true;        
            }
        }

    }
}

/**
* Generate the container for timeline and set options
* and groups
*/
function generateContainer()
{
    if (set_container === false)
    {
        // we create the container with the size of the client            
        container = document.getElementById('visualization');
        usable_height = $(window).height() - $('#menunav').outerHeight() - parseInt($('body').css('margin-bottom'));
        usable_height = usable_height - 10/100*usable_height;
        options.minHeight = usable_height;
        options.maxHeight = usable_height;            

        timeline = new vis.Timeline(container);
        timeline.setOptions(options);
        timeline.setGroups(groups);

        set_container = true;                                    
    }
}    

/**
* Make sure all events has the corresponing tooltips    
*/
function checkTooltips()
{
    data_elements = $('[data-json_string]');
    if (data_elements.length > 0)
    {
        $('[data-json_string]').qtip({ // Grab all elements with a non-blank data-tooltip attr.
            content: {
                attr: 'data-json_string' // Tell qTip2 to look inside this attr for its content
            },                
            position: {
                my: 'bottom center',  
                at: 'top center' 
            },                        
            style: {
                classes: 'qtip-jtools nice_json' //nice_json
            }
            
        });
    }
}    


/**
* Convert the date string to more human friendly
*
* @datestring from javascrtip event date
* @return the date with better looking
*/
function getHumanDate(datestring)
{
    var dateObj = new moment(datestring);
    var splitted_date = dateObj.toString().split('\ ');
    if (splitted_date.length >= 6)
    {
        var human_date = splitted_date.slice(0, 5).join('\ ');
        return human_date;          
    }
    console.log('Something bad with the date!');
    return '';
    
}    

/**
* Get the current config data of the user (integrations)
* and start and end of the timeline at the current time
*
* @return the link string to share
*  
*/
function getShareLink()
{
    var settings_data = {};        

    for (x=0; x<selected_integrations.length; x++ )
    {
        settings_data['Integrations' + x.toString()] = selected_integrations[x].Integration_Name;            
    }

    current_range = timeline.getWindow();

    settings_data['start'] = current_range.start.getTime();
    settings_data['end'] = current_range.end.getTime();

    var url_params = $.param(settings_data);

    var share_link = window.location.protocol + '//' + window.location.hostname + '/dashboard?' + url_params;

    return share_link;

}

/**
* Fix the images from the events that for some reason it's a 404    
*    
*/
function fix404Images() 
{
    var images = document.getElementsByTagName('img'); 

    for(i=0;i<images.length;i++){
        images[i].onerror=images[i].src='/images/webhook_custom.png';
    }
};




//we make the Dataset instance for later update it
items = new vis.DataSet();





/**
* makes the NewRelic event content based on the @event_data from the 
* ajax query to the api and update the items with the the data
* generated
*
* @event_data is the json from the integration data
* @custom_group is the group number of the integration  
*/
function addNewRelicItem(event_data, custom_group)
{    
    var incident_id = event_data.incident_id;
    var event_link = '<a target="_blank" href="' + event_data.incident_url +  '">' + incident_id + '</a>';
    //var event_content = '<img  src="/images/users_photos/' + event_data.username + '.jpg" width="30" height="30" hspace="3" ' + 'onerror="this.src=' + "'/images/webhook_custom.png'" + ';">' +  event_link;                            
    var event_content = '<img  src="/images/NewRelic-logo-square_small.png"  width="45" height="36" ' + 'onerror="this.src=' + "'/images/webhook_custom.png'" + ';">'  + event_link;
    var event_start = event_data.timestamp;

    var event_condition_name = '<div class="tooltip-title">' + event_data.condition_name + '</div>';
    var event_description = '<p>' + event_data.details + '</p>';
    var event_type = '<p><span class="json-key tooltip-label">TYPE:</span>' + event_data.event_type + '</p>';
    var event_app_name = '<p><span class="json-key tooltip-label">APP NAME: </span>' + JSON.stringify(event_data.targets) + '</p>';
    var event_message = '<p><span class="json-key tooltip-label">MESSAGE: </span>' + event_data.details + '</p>';

    var event_json = event_condition_name + event_description + event_type + event_app_name + event_message;                      


    


    var item_data = {
        id: event_data._id,
        group: custom_group,
        content: event_content,
        start: event_start,
        type: 'box',
        className: newRelic_statuses[event_data.current_state],
        json_string: event_json
    }

    items.update(item_data);        

    
}

/**
* makes the Pagerduty event content based on the @event_data from the 
* ajax query to the api and update the items with the the data
* generated
*
* @event_data is the json from the integration data
* @custom_group is the group number of the integration  
*/
function addPagerdutyItem(event_data, custom_group)
{
    var event_link = '<a target="_blank" href="' + event_data['html_url'] +  '">' + event_data.id + '</a>';    
    var event_content = '<img  src="/images/pdlogo.png" width="20" height="20" ' + 'onerror="this.src=' + "'/images/webhook_custom.png'" + ';">' + event_link;
    var event_id = event_data['id'];
    var event_start = event_data['created_on'];


    // we do different things depending on the content of the JSON
    if (event_data.trigger_summary_data.subject !== undefined)
    {
        var event_subject_name = '<div class="tooltip-title">' + event_data.trigger_summary_data.subject + '</div>';    
    }
    else                                
    {
        var title_header =  event_data.trigger_summary_data.HOSTNAME + ': ' + event_data.trigger_summary_data.SERVICEDESC + ' ' + event_data.trigger_summary_data.HOSTSTATE;
        var event_subject_name = '<div class="tooltip-title">' + title_header + '</div>';       
    }

    
    var event_data_id = '<p><span class="json-key tooltip-label">EVENT ID: </span>' + event_data.id + '</p>';
    var event_status = '<p><span class="json-key tooltip-label">STATUS: </span>' + event_data.status + '</p>';

    var event_description = '<p></p>';
            
    //we do it differently in case of status resolved
    if (event_data.status == 'resolved')
    {
        if (event_data.resolved_by_user == undefined)
        {
            var event_assigned_to = '<p><span class="json-key tooltip-label">RESOLVED BY: </span>' + 'API' + '</p>';
        } else {
            if (event_data.resolved_by_user.name == null)
            {
                var event_assigned_to = '<p><span class="json-key tooltip-label">RESOLVED BY: </span> API </p>';
            }
            else
            {
                var event_assigned_to = '<p><span class="json-key tooltip-label">RESOLVED BY: </span>' + event_data.resolved_by_user.name + '</p>';
            }
                  
        }
    }
    else 
    {
        if (event_data.assigned_to_user.name == null)
        {
            var event_assigned_to = '<p><span class="json-key tooltip-label">ASSIGNED TO: </span> none </p>';     
        }
        else
        {
            var event_assigned_to = '<p><span class="json-key tooltip-label">ASSIGNED TO: </span>' + event_data.assigned_to_user.name + '</p>';     
        }
        
    }

    var created_on = '<p><span class="json-key tooltip-label">CREATED ON: </span>' +  getHumanDate(event_start) + '</p>';                                                                                  
    var event_json = event_subject_name + event_description + event_data_id + event_status + event_assigned_to + created_on;    

    event_status = pagerduty_statuses[event_data.status];

    var item_data = {
        id: event_id,
        group: custom_group,
        content: event_content,
        start: event_start,
        end: event_data.last_status_change_on,
        type: 'range',
        className: event_status,
        json_string: event_json
    }

    if (event_data.status !== 'resolved')
    {

        item_data.type = 'box';
        delete item_data.end;            
    }

    items.update(item_data);
    
}

/**
* makes the Custom user event content based on the @event_data from the 
* ajax query to the api and update the items with the the data
* generated
*
* @event_data is the json from the integration data
* @custom_group is the group number of the integration  
*/
function addCustomItem(event_data, custom_group)
{
            
    var event_content = '<img  src="/images/users_photos/' + event_data.username + '.jpg" width="30" height="30" hspace="3" ' + 'onerror="this.src=' + "'/images/webhook_custom.png'" + ';">' +  event_data.username;
    var event_id = event_data._id;

    if (event_data.start == undefined)
    {
        var event_start = event_data.start_date;    
    }
    else
    {
        var event_start = event_data.start;   
    }        
                                
    var event_data_username = '<p><span class="json-key tooltip-label">USERNAME: </span>' + event_data.username + '</p>';

    //we convert the Slack Markdown to html and we remove the p tags
    var marked = lightMarkdown.toHtml(event_data.message).replace('<p>', '').replace('</p>', '');        

    //we convert the emojis to the real icons
    var emojified = emojify.replace(marked);

    //we remove the bad html entites generated by slack
    emojified = emojified.replace('&amp;lt;', '<').replace('&amp;gt;', '>');

    emojified = emojified.replace('&lt;', '<').replace('&gt;', '>');

    emojified = linkify(emojified).replace('<<', '<').replace('>>', '>');
    
    var event_message = '<p><span class="json-key tooltip-label">MESSAGE: </span>' + emojified  + '</p>';
    
    var created_on = '<p><span class="json-key tooltip-label">CREATED ON: </span>' + getHumanDate(event_start) + '</p>';
    var event_json = event_data_username + event_message + created_on;

    //console.log(event_content); 

    item_data = {
        id: event_id,
        group: custom_group,
        content: event_content,
        start: event_start,
        type: 'box',
        json_string: event_json                                 
    }

    items.update(item_data);
}




function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}


/**
* This function will be needed in case there is too
* many events in little time, if that occurs the timeline
* gets very slow and unresponsive
*
* @event_data is the json from the integration data
* @custom_group is the group number of the integration  
*/
function reduceRange()
{
    // if the zoomMax has not been reduced
    // we try to reduce the zoom out to show 
    // less events
    if (options.zoomMax > 302400000)
    {
        options.zoomMax = Math.floor(options.zoomMax / 2);
        timeline.setOptions(options);    
    } 
            
    var reduced_items = new vis.DataSet();            
    $.each(items.getIds(),  function(index, id) {
        if (index === 0)
        {
            item = items.get(id);
            reduced_start = new Date(item.start);
        }                
        if (index < 599)
        {
            item = items.get(id);    
            reduced_items.add(item);
        }
        if (index === 599)
        {                
            item = items.get(id);
            reduced_end = new Date(item.start);                
        }                                            
    });

    if (reduced_items.length > 0)
    {
        items = reduced_items;
        start = reduced_start;
        end = reduced_end; 
    }         
    
}


/**
* This function checks if we have reached the limit
* of the events date from start or from end
* if we have reached the limit we get the next limits
* and get the new events for the new range
*
* @properties is the parameter sent by the event    
*/
function getItemsRange(properties)
{       


    if (properties.end > getNextDate(end, 1))
    {            
        items.clear();
        //we separate one day to have margin
        start = getNextDate(properties.start, -1);
        //start = new Date(properties.start ;
        end = getNextDate(properties.start, days_interval);
        console.log([properties.start, properties.end]);            
        console.log(['generateDashboard right', start, end]);            
        generateDashboard(start, end);        
                    
    }
    else 
    {
        if (properties.start < getNextDate(start, -1))
        {                
            items.clear();
            end = getNextDate(properties.end, 1);
            //end = properties.end;                
            start = getNextDate(properties.end, -days_interval);
            console.log([properties.start, properties.end]);
            console.log(['generateDashboard left', start, end]);                
            generateDashboard(start, end);                            
        }
    }

    checkTooltips();

    
}


/**
* This function traverse all the integrations we need and  
* make the Ajax request to get all the integration data
* using start and end to get the event in that time range 
* 
* @start this is the start date to get events from the API
* @end this is the end limit to get events from the API    
*/  
function generateDashboard(start, end, integration_only)
{        
            
    generateGroups();
    generateContainer();
    $('#ajaxLoading').show();

    console.log(start, end);

    

    // we run over the integrations 
    for (var g = 0; g < selected_integrations.length; g++) 
    {            

        var sel_integration = selected_integrations[g];            

        if ((typeof(integration_only) !== 'undefined') && 
            (integration_only !== sel_integration.Integration_Name))
        {
            continue;
        }
                                            
        var search_url = url_start + '/webhooks/integrations/' + sel_integration.Integration_Name + '/search';

        var post_data = {
            search: '',
            type: sel_integration.type,
            start_date: start.toISOString(),
            end_date: end.toISOString()
        }

        console.log(search_url);
        console.log(JSON.stringify(post_data));

                    
        $.ajax({
            'type': 'POST',
            url: search_url,
            contentType: 'application/json',
            data: JSON.stringify(post_data),
            custom_group: g,
            selected_integration: sel_integration,                                
            success: function(integration_data) {

                //we update last request from server
                integration_list[sel_integration.Integration_Name].last_update = new Date();                                    


                try {
                    switch (this.selected_integration.type) {
                        case 'newrelic':
                            for (var i = 0; i <= (integration_data.length - 1); i++) 
                            {                    
                                event_data = integration_data[i];                                                                  
                                addNewRelicItem(event_data, this.custom_group);
                            }                            
                            break;
                        case 'pagerduty':
                            for (var i = 0; i <= (integration_data.length - 1); i++)
                            {
                                event_data = integration_data[i];
                                addPagerdutyItem(event_data, this.custom_group);    
                            }                                                        
                            break;    
                        case 'custom':
                            for (var i = 0; i <= (integration_data.length - 1); i++)
                            {
                                event_data = integration_data[i];
                                addCustomItem(event_data, this.custom_group);
                            }                                                    
                            break;
                    }                                                                                            
                } 
                catch (err) {
                    console.log("Error", err.stack);
                    console.log("Error", err.name);
                    console.log("Error", err.message);
                }

                                                                                                            

                // if we are on the last integration 
                if (this.custom_group == (selected_integrations.length - 1))
                {
                    timeline.setItems(items);                                        
                    checkTooltips();
                    timeline.off('rangechanged', getItemsRange);                       
                    timeline.on('rangechanged', getItemsRange);                                       
                }
                

            },        
            error: function(xhr, status, error) {                    
                alert(error.Message);
            },
            dataType: 'json'
        });
        
    }

    
}



function goToNow()
{
    //we set up the time range on now
    var current_time = timeline.getCurrentTime();
    current_time.setHours(current_time.getHours() + 1);
    var before_time = new Date(current_time);
    before_time.setHours(current_time.getHours() - 6);
    timeline.setWindow(before_time, current_time);
}



/**
* We add the selected_integrations to integration_list
*     
*/
function setIntegrationList()
{        
    for (i=0; i<selected_integrations.length; i++) { 
        var integration_data = {last_update: new Date(), integration_group: i};
        integration_list[selected_integrations[i].Integration_Name] = integration_data;            
    }
}



groups = new vis.DataSet();
set_groups = false;
set_container = false;
set_event = false;        

if (typeof start == 'undefined') {
    end = new Date();
    start = getNextDate(end, -days_interval);
}

//basic options config for timeline
options = {
    //configure: true,
    groupOrder: 'content',  // groupOrder can be a property name or a sorting function
    editable: false,   // default for all items
    // end: moment().minutes(0).seconds(0).milliseconds(0)
    groupEditable: false,
    width: "100%",
    margin: {
           item: 10,
      },
    minHeight: '',
    maxHeight: '',                
    zoomMin: 1000000,
    //zoomMax: 604800000,
    zoomMax: 1209600000,
    dataAttributes: 'all'
    //start: new Date() 
};


newRelic_statuses = {
    OPEN: 'red',
    open: 'red',                    
    ACKNOWLEDGED: 'orange',
    acknowledged: 'orange',    
    CLOSED: 'green',
    closed: 'green',                    
    CLOSE: 'green',
    close: 'green'
};

pagerduty_statuses = {
    triggered: 'red',
    acknowledged: 'orange',
    resolved: 'green'                    
};


setIntegrationList();

if (param_start !== false)
{        
    start = param_start; 
    end = param_end;
    generateGroups();        
    generateContainer();
    timeline.setWindow(param_start, param_end);
    
} else {    
    generateGroups();        
    generateContainer();    

    setTimeout(function() {   //calls click event after a certain time
        goToNow();
    }, 400);

    
}


generateDashboard(start, end);

//and setup the new one
























function zoom (percentage) {
    var range = timeline.getWindow();
    var difference = range.end - range.start;
    
    timeline.setWindow({
            start: range.start.valueOf() - difference * percentage,
            end:   range.end.valueOf()   + difference * percentage,
            animation: false
            //duration: 100, easingFunction: "linear"
        }            
    );
}

function move (percentage) {
    var range = timeline.getWindow();
    var difference = range.end - range.start;

    timeline.setWindow({
            start: range.start.valueOf() - difference * percentage,
            end:   range.end.valueOf()   - difference * percentage,
            animation: false
            //duration: 50, easingFunction: "easeOutQuad"
        }            
    );
}





  $(document).ready(function() {
    $('#zoomIn').mousedown(function(){
        interval = setInterval(function(){zoom(-0.03)}, 35);
        //zoom(-0.2);
    });
    $('#zoomIn').mouseup(function(){
        clearInterval(interval);
        //zoom(0.2);
    });

    $('#zoomOut').mousedown(function(){
        interval = setInterval(function(){zoom(0.03)}, 35);
        //zoom(0.2);
    });
    $('#zoomOut').mouseup(function(){
        clearInterval(interval);
        //zoom(0.2);
    });
    $('#moveLeft').mousedown(function(){
        interval = setInterval(function(){move(0.03)}, 35);             
    });
    $('#moveLeft').mouseup(function(){
        clearInterval(interval);             
    });

    $('#moveRight').mousedown(function(){
        interval = setInterval(function(){move(-0.03)}, 35);             
    });
    $('#moveRight').mouseup(function(){
        clearInterval(interval);             
    });

    // $('#moveRight').click(function(){
    //      move(-0.2);
    // });
    $('#button1').click(function(){        
        goToNow();

    });




    $('#share').click(function(){
        
        //we clean the data in case this was shared before
        var shared_link = getShareLink();            
        $('#linktext').empty();
        $('#linktext').append(shared_link);
        $('#linktext').on('mouseup', function() { $(this)[0].select(); });            

        var clipboard = new Clipboard('#copyToClipboard');

        clipboard.on('success', function(e) {               
            //we add the toll tip
            $('#linktext').qtip({ // Grab all elements with a non-blank data-tooltip attr.
                content: {
                    text: 'Link Copied!',                       
                    show: true
                },                  
                position: {
                    my: 'bottom right',  
                    at: 'top center' 
                },                        
                style: {
                    classes: 'qtip-jtools nice_json' //nice_json
                }
            
            });
            $('#linktext').qtip('toggle', true);

        });

        // in case there is some error we guess is a MAC
        clipboard.on('error', function(e) {
            $('#linktext').qtip({ // Grab all elements with a non-blank data-tooltip attr.
                content: {
                    text: '⌘ + C',                      
                    show: true
                },                  
                position: {
                    my: 'bottom right',  
                    at: 'top center' 
                },                        
                style: {
                    classes: 'qtip-jtools nice_json' //nice_json
                }
            
            });
            $('#linktext').qtip('toggle', true);
            
        });


    });



    setInterval(function(){

        // we check every 10 seconds if there is updates from the daemon
        $.ajax({        
            url: window.location.protocol + "//" + window.location.hostname + "/event_logs/all_logs.log",                                
            success: function(response_data) {
                //console.log(response_data);
                var last_events = JSON.parse(response_data);

                for (x=0; x<last_events.length; x++)
                {
                    var l_event = last_events[x];
                    if ((l_event) && (l_event.integration_name in integration_list))                            
                    {
                        var integration_data = integration_list[l_event.integration_name];                            

                        switch(l_event.integration_type) {
                            case 'pagerduty':
                                addPagerdutyItem(l_event.json_data, integration_data.integration_group);
                                break;
                            case 'newrelic':
                                addNewRelicItem(l_event.json_data, integration_data.integration_group);
                                break;
                            case 'custom':
                                addCustomItem(l_event.json_data, integration_data.integration_group);
                        }  
                    }
                }                                        

            },
            error: function(xhr, status, error) {
                //var err = JSON.parse(xhr.responseText) ;
                console.log(err.Message);
                console.log(xhr.responseText);
                console.log(status);                    
            }                               
        })

    }, 10000);
            


  });    
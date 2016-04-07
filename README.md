# Timeline

Timeline is a dashboard web interface for manage and monitorize events from Incidents Management Systems like Pagerduty, New Relic and custom made events. You can visually see the events in a easily adaptable and browseable time scale. All the events info and the integrations are saved on a Mongodb database.

**This system is intended to be used as internal service on companies with secure environments behind firewall or VPN. The internal APIs don't have authentication and the access should
 be restricted.**
 
# Requirements
 - Apache2 
 - Mongodb 3.x
 - Supervisor

# Install 

```git clone https://github.com/upwork/timeline```

The system deployment consist on two virtual hosts, one (timeline_ext_api) will be open to Internet to be accessed from Pagerduty, New Relic, or other external webhooks. The other (timeline) has the API and the web interface and will be enabled for sending events internally via API interface.

The interface has an LDAP authentication system, which can be configured on the timeline/config.php file.

There is Apache config samples on INSTALL folder.

There is a daemon that write events to file to give access to last events updates:

event_logs/log_monitor_piper.php

This daemon is managed by supervisor, a supervisor config file has been included also on INSTALL folder to make sure events go almost instantly to the interface.

There is a sample database with integrations ready to install to make it work:

```mongorestore timeline_dump_sample/```

Configure the database on interface config file timeline/config.php the api config file: timeline/api/config.php and the external API config file timeline_ext_api/api/config.php

Make sure 'events_logs_path' => '/var/www/html/timeline/event_logs/' has the correct path to log_monitor_piper.php

For custom events you can have user images so the events will show the user images, you can add your images on images/user_photos/ the filename will be used as the username, and must be jpg images.

Example custom event:

```
{ "start_date": "2016-02-18T15:23:04.0Z",
  "username": "josegarcia",
  "message": "This is an event" }
  ```

You will need a josegarcia.jpg image on images/user_photos/ to show the image on the event


# Development

To generate the minified Javascript and the CSS there is a deploy_production.sh script on INSTALL to generate the minified versions that will be used on production mode

This script is using [minifier](https://www.npmjs.com/package/minifier) npm package 

There is a also  PHPUnit scripts on tests folder to generate events you can generate events editing the IP parameters on the code.


# License

MIT 

**Free Software, Hell Yeah!**

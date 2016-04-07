#!/usr/bin/env




# Minify dashboard

minify --output js/dashboard_all-minified.js js/jquery.min.js js/bootstrap.min.js js/vis.js js/moment.min.js js/light_spinner/ajax-loading.js js/jquery.qtip.custom/jquery.qtip.js js/clipboard.js js/lightMarkdown.js js/emojify.js js/custom_dashboard.js

minify --output css/vis-min.css css/vis.css
minify --output css/bootstrap_dark-min.css css/bootstrap_dark.css
minify --output css/sticky-footer-min.css css/sticky-footer.css
minify --output css/bootstrap.icon-large.min-min.css css/bootstrap.icon-large.min.css
minify --output css/custom_dashboard-min.css css/custom_dashboard.css
minify --output js/jquery.qtip.custom/jquery.qtip-min.css js/jquery.qtip.custom/jquery.qtip.css
minify --output js/json_human/json.human-min.css js/json_human/json.human.css
minify --output css/emojify-min.css css/emojify.css

echo -n "" > css/dashboard_all-minified.css

cat css/vis-min.css css/bootstrap_dark-min.css css/sticky-footer-min.css css/bootstrap.icon-large.min-min.css css/custom_dashboard-min.css js/jquery.qtip.custom/jquery.qtip-min.css js/json_human/json.human-min.css css/emojify-min.css >> css/dashboard_all-minified.css

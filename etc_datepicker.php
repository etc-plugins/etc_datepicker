<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'etc_datepicker';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.3.2';
$plugin['author'] = 'Oleg Loukianov';
$plugin['author_uri'] = 'http://www.iut-fbleau.fr/projet/etc/';
$plugin['description'] = 'Datepicker UI widget';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '3';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

/** Uncomment me, if you need a textpack
$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
abc_sample_string => Sample String
abc_one_more => One more
#@language de-de
abc_sample_string => Beispieltext
abc_one_more => Noch einer
EOT;
**/
// End of textpack

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
register_callback('etc_datepicker', 'admin_side', 'body_end');

function etc_datepicker($event, $step)
{
$script = <<<JS
$(document).ready(function () {
textpattern.Relay.register('txpAsyncForm.success', etcDatepicker);

$(document).keydown(function (e) {
    if (e.which === 27) {
        $('.hasDatepicker').hide();
    }
});

function etcDatepicker() {
    if (!$('.txp-datepicker-button').length)
        $('.input-day').after('&nbsp;<a class="txp-datepicker-button" href="#" title="Datepicker" aria-label="Datepicker"><span class="ui-icon ui-icon-calendar">Datepicker</span></a>');

    $('.txp-datepicker-button').click(function(e) {
        e.preventDefault();
        $(this).parent().children('.mypicker').toggle();
        e.stopPropagation();
    }).after('<div style="display:none;position:absolute;z-index:100" class="mypicker" />');

    $('.mypicker').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            showMonthAfterYear: true,
            onSelect: function(date, inst) {
                var div = $(this).hide().parent();
                var date = $(this).datepicker('getDate');
                $(this).datepicker('option', 'defaultDate', date).datepicker('setDate', date);
                div.children('.input-day').val(('0'+inst.selectedDay).slice(-2));
                div.children('.input-month').val(('0'+(inst.selectedMonth + 1)).slice(-2));
                div.children('.input-year').val(inst.selectedYear);
            }
        }
    ).click(function(e) {
        e.stopPropagation();
    });

    $('body').bind('click touchstart', function(e) {
        $('.mypicker').hide();
    });

    $('.input-year, .input-month, .input-day').change(function () {
            if (!$(this).val()) return;
            var div = $(this).parent();
            var date = new Date(div.children('.input-year').first().val(), div.children('.input-month').first().val() - 1, div.children('.input-day').first().val());
            div.children('.mypicker').datepicker('option', 'defaultDate', date).datepicker('setDate', date);
    });

    $('.input-year').change();
}

etcDatepicker();
});
JS;
echo script_js($script);
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---

# --- END PLUGIN HELP ---
-->
<?php
}
?>

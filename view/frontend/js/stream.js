jQuery(document).ready(function(){

    jQuery('.widget_livestream_widget').each(function(){
        if(jQuery('.widget_livestream_widget .updator').length) {
            var full_width = jQuery(this).find('.updator').innerWidth();
            var textarea_width = Math.ceil(jQuery(this).find('.updator textarea').outerWidth()+5);
            var textarea_height = jQuery(this).find('.updator textarea').outerHeight();

            var submit_button_marginleft = jQuery(this).find('.updator input').css('margin-left').replace('px','');
            var submit_button_marginright = jQuery(this).find('.updator input').css('margin-right').replace('px','');
            var submit_button_paddingleft = jQuery(this).find('.updator input').css('padding-left').replace('px','');
            var submit_button_paddingright = jQuery(this).find('.updator input').css('padding-right').replace('px','');

            var submit_padding = parseInt(submit_button_marginleft)+parseInt(submit_button_marginright)+parseInt(submit_button_paddingleft)+parseInt(submit_button_paddingright);

            //jQuery(this).find('.updator input').css('min-width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('max-width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('min-height',parseInt(textarea_height));
            //jQuery(this).find('.updator input').css('height',parseInt(textarea_height));
        }

        jQuery(this).find('img').each(function() {
            var unsizedsrc = jQuery(this).attr('unsizedsrc');
            var width = jQuery(this).parent().width();
            if(width > jQuery(this).attr('maxwidth'))
                width = jQuery(this).attr('maxwidth');
            var timthumb = livestream_url+'/view/frontend/timthumb.php?src='+unsizedsrc+'&w='+width;
            jQuery(this).attr('src', timthumb).show();
        });
    });

    liveStream_init_bitly();

    jQuery('.widget_livestream_widget .updator input').click(function(){
        var the_widget = jQuery(this).parent().parent().parent();
        var i = 20;
        while((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container')) && i-- > 0)
            the_widget = the_widget.parent();


        if(jQuery(the_widget).hasClass('pending')) return;
        if(jQuery.trim(jQuery(the_widget).find('textarea').val()) == '' || jQuery.trim(jQuery(the_widget).find('textarea').val()) == 'type your message here') return;

        var update_networks = '';
        jQuery(the_widget).find('.update_networks input[type=checkbox]:checked').each(function() {
            update_networks += jQuery(this).val();
        });

        var params = {
            livestream_update : jQuery(the_widget).find('textarea').val(),
            charlimit : border.attr('charlimit'),
            feedcount : jQuery(the_widget).find('.livestream_widget_border').attr('feedcount'),
            networks : update_networks,
            updatemessage : jQuery(the_widget).find('#update_message').text()
        };

        var datebordercolor = jQuery(the_widget).find('.livestream_widget_border').attr('datebordercolor');
        if(datebordercolor) params.datebordercolor = datebordercolor;

        var datebackgroundcolor = jQuery(the_widget).find('.livestream_widget_border').attr('datebackgroundcolor');
        if(datebackgroundcolor) params.datebackgroundcolor = datebackgroundcolor;

        var bubblebordercolor = jQuery(the_widget).find('.livestream_widget_border').attr('bubblebordercolor');
        if(bubblebordercolor) params.bubblebordercolor = bubblebordercolor;

        var bubblebackgroundcolor = jQuery(the_widget).find('.livestream_widget_border').attr('bubblebackgroundcolor');
        if(bubblebackgroundcolor) params.bubblebackgroundcolor = bubblebackgroundcolor;

        var showold = jQuery(the_widget).find('.livestream_widget_border').attr('showold');
        if(showold) params.showold = showold;

        var attachment = jQuery(the_widget).find('.media-upload input').val();
        if(attachment) params.attachment = attachment;

        var streamid = jQuery(the_widget).find('.livestream_widget_border').attr('streamid');
        if(streamid) params.streamid = streamid;

        jQuery(the_widget).addClass('pending');
        jQuery.ajax({
            type:'POST',
            url: livestream_site_url+'/',
            data: params,
            success : function(data) {
                jQuery(the_widget).removeClass('pending');
                jQuery(the_widget).find('dl.livestream_feed').replaceWith(data);
                jQuery(the_widget).find('textarea').val('type your message here');
                jQuery(the_widget).find('.media-upload input').val('');
                jQuery(the_widget).find('.media-upload .swfupload-progress').hide();
                jQuery(the_widget).find('.deattachment-placeholder').hide();

                jQuery(the_widget).find('img').each(function() {
                    var unsizedsrc = jQuery(this).attr('unsizedsrc');
                    var width = jQuery(this).parent().width();
                    if(width > jQuery(this).attr('maxwidth'))
                        width = jQuery(this).attr('maxwidth');
                    var timthumb = livestream_url+'/view/frontend/timthumb.php?src='+unsizedsrc+'&w='+width;
                    jQuery(this).attr('src', timthumb).show();
                });
            },
            error : function(data) {
                jQuery(the_widget).removeClass('pending');
                alert(data);
            },
            cache: false
        });
    });

    // view older feeds

    jQuery('.widget_livestream_widget .livestream_feed .more').live('click', function(){
        liveStream_feedupdate(true);
    });

    jQuery('.widget_livestream_widget textarea').bind('change keyup', function() {
        var the_widget = jQuery(this).parent().parent().parent();
        var i = 20;
        while((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container')) && i-- > 0)
            the_widget = the_widget.parent();
        var maxlen = parseInt( the_widget.find('.livestream_widget_border').attr('charlimit') );

        if(maxlen > 0 && liveStream_input_length(jQuery(this).val()) > maxlen) {
            var top = jQuery(this).scrollTop();
            jQuery(this).val(jQuery(this).val().substr(0, liveStream_input_length_max( jQuery(this).val(), maxlen )));
            jQuery(this).scrollTop(top);
        }
    });

    jQuery('.widget_livestream_widget textarea').focus(function() {
        if(jQuery(this).val() == 'type your message here')
            jQuery(this).val('');
    });
    jQuery('.widget_livestream_widget textarea').blur(function() {
        if(jQuery.trim(jQuery(this).val()) == '')
            jQuery(this).val('type your message here');
    });


// social network stuff

// twitter
    jQuery('.livestream_widget_border .twitter input').live('click', function() {
        if(jQuery(this).hasClass('noauth')&&jQuery(this).is(':checked'))
            window.open('?livestream_auth=twitter', '', 'width=800,height=440');
    });

    jQuery('.livestream_widget_border .twitter div.icon').live('click', function(){
        var checked = 'checked';
        if(jQuery(this).next().is(':checked'))
            checked = false;
        jQuery(this).next().attr('checked',checked);
        jQuery(this).next().click();
        jQuery(this).next().attr('checked',checked);
    });

// swfupload stuff

    if(jQuery('.widget_livestream_widget .updator').length)
    jQuery('.widget_livestream_widget').each(function(){
        var progress_id = jQuery(this).find('.swfupload-progress').attr('id');
        var placeholder_id = jQuery(this).find('.swfupload-placeholder').attr('id');

        settings = {
            upload_url : livestream_site_url + "/",
            flash_url : livestream_site_url + "/wp-includes/js/swfupload/swfupload.swf",

		    post_params: {"PHPSESSID" : "", "livestream_upload":"1"},
		    file_size_limit : livestream_max_upload+" MB",
		    file_types : "*.png; *.jpg; *.bmp; *.gif",
		    file_types_description : "Images",
		    file_upload_limit : livestream_max_upload,
		    file_queue_limit : 0,
		    debug: false,
		    custom_settings : {
        		button_placeholder_id: progress_id,
        		sizelimit : livestream_max_upload
		    },


            button_image_url: livestream_url + "/view/frontend/css/images/media-button.png",
		    button_width: "15",
		    button_height: "15",
		    button_placeholder_id: placeholder_id,

		    file_queued_handler : liveStream_swfhandles.fileQueued,
		    file_queue_error_handler : liveStream_swfhandles.fileQueueError,
		    file_dialog_complete_handler : liveStream_swfhandles.fileDialogComplete,
		    upload_start_handler : liveStream_swfhandles.uploadStarted,
		    upload_progress_handler : liveStream_swfhandles.uploadProgress,
		    upload_error_handler : liveStream_swfhandles.uploadError,
		    upload_success_handler : liveStream_swfhandles.uploadSuccess,
		    upload_complete_handler : liveStream_swfhandles.uploadComplete,
		    queue_complete_handler : liveStream_swfhandles.queueComplete

        };
        jQuery(this).find('.swfupload-progress').hide();
        new SWFUpload(settings);
    });

// attachment stuff

    jQuery('.deattachment-placeholder').live('click', function() {
        jQuery(this).parent().find('.swfupload-progress').html('');
        jQuery(this).parent().find('input').val('');
        jQuery(this).hide();
    });

});

jQuery(window).load(function() {

    jQuery('.widget_livestream_widget').each(function(){
        if(jQuery('.widget_livestream_widget .updator').length) {
            var full_width = jQuery(this).find('.updator').innerWidth();
            var textarea_width = Math.ceil(jQuery(this).find('.updator textarea').outerWidth()+5);
            var textarea_height = jQuery(this).find('.updator textarea').outerHeight();

            var submit_button_marginleft = jQuery(this).find('.updator input').css('margin-left').replace('px','');
            var submit_button_marginright = jQuery(this).find('.updator input').css('margin-right').replace('px','');
            var submit_button_paddingleft = jQuery(this).find('.updator input').css('padding-left').replace('px','');
            var submit_button_paddingright = jQuery(this).find('.updator input').css('padding-right').replace('px','');

            var submit_padding = parseInt(submit_button_marginleft)+parseInt(submit_button_marginright)+parseInt(submit_button_paddingleft)+parseInt(submit_button_paddingright);
            //
            //jQuery(this).find('.updator input').css('min-width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('max-width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('width',parseInt(full_width-(textarea_width + submit_padding)));
            //jQuery(this).find('.updator input').css('min-height',parseInt(textarea_height));
            //jQuery(this).find('.updator input').css('height',parseInt(textarea_height));
        }
    });

});

var settings =  null;

setInterval("liveStream_feedupdate(false)",1000);
function liveStream_feedupdate(_showold) {
    jQuery('.widget_livestream_widget').each(function() {
        var the_widget = jQuery(this);
        if(jQuery(the_widget).hasClass('pending')||jQuery(the_widget).hasClass('polling')) return;
        border = jQuery(the_widget).find('.livestream_widget_border');

        if(!_showold && the_widget[0].livestream_countdown == undefined) {
            the_widget[0].livestream_countdown = border.attr('interval');
            return;
        }
        if(_showold || !the_widget[0].livestream_countdown)
            the_widget[0].livestream_countdown = border.attr('interval');
        else {
            --the_widget[0].livestream_countdown;
            return;
        }

        var params = {
            livestream_fetchfeed : border.attr('lastupdate'),
            charlimit : border.attr('charlimit'),
            feedcount : border.attr('feedcount'),
            widgetid : jQuery(the_widget).attr('id').replace('livestream_widget-',''),
            title : border.attr('streamtitle'),
            updatemessage : jQuery(the_widget).find('#updatemessage').text()
        };

        var datebordercolor = border.attr('datebordercolor');
        if(datebordercolor) params.datebordercolor = datebordercolor;

        var datebackgroundcolor = border.attr('datebackgroundcolor');
        if(datebackgroundcolor) params.datebackgroundcolor = datebackgroundcolor;

        var bubblebordercolor = border.attr('bubblebordercolor');
        if(bubblebordercolor) params.bubblebordercolor = bubblebordercolor;

        var bubblebackgroundcolor = border.attr('bubblebackgroundcolor');
        if(bubblebackgroundcolor) params.bubblebackgroundcolor = bubblebackgroundcolor;

        var showold = border.attr('showold');
        if(showold) params.showold = showold;
        if(_showold) {
            params.showolder = 'true';
            border.attr('showold', 'true');
        }

        var streamid = border.attr('streamid');
        if(streamid) params.streamid = streamid;

        jQuery(the_widget).addClass('polling');
        jQuery.ajax({
            type:'POST',
            url: livestream_site_url + "/",
            data: params,
            success : function(data) {
                jQuery(the_widget).removeClass('polling');
                if(!data.length)
                    return;
                var parsed = data.split('<!-!>');
                var update_networks = '';
                if(parsed.length > 2) {
                    if(parsed[0])
                        jQuery(the_widget).find('.widget-title').html(parsed[0]);


                    var height = jQuery(the_widget).attr('livestream_feed_height');
                    if(!height && border.attr('showold') != 'false') {
                        height = jQuery('.livestream_feed').height() - 20;
                        jQuery(the_widget).attr('livestream_feed_height', height);
                    }

                    var scroll = jQuery(the_widget).find('.livestream_feed').scrollTop();

                    border.attr('lastupdate', parsed[1]);
                    jQuery(the_widget).find('dl.livestream_feed').replaceWith(parsed[2]);
                    update_networks = parsed[3];

                    // rescroll bar it
                    if(border.attr('showold') != 'false') {
                        jQuery(the_widget).find('.slimScrollDiv').replaceWith(jQuery(the_widget).find('.livestream_feed'));

                        jQuery(the_widget).find('.livestream_feed').slimScroll({
                            height: height+'px',
                            scroll: scroll + 'px'
                        });
                        jQuery(the_widget).find('.livestream_feed').slimScroll({
                            scroll: scroll + 'px'
                        });
                        jQuery(the_widget).find('.media-upload').css('margin-top','20px');

                        jQuery(the_widget).find('.livestream_widget_border').attr('showold', parsed[4]);
                        jQuery(the_widget).find('.livestream_widget_border dd:last').css('margin-bottom','0px');
                    }

                } else {
                    jQuery(the_widget).find('.widget-title').html(parsed[0]);
                    update_networks = parsed[1];
                }

                var network_list = update_networks.split(',');
                for(i = 0; i < network_list.length; ++i) {
                    var network_split = network_list[i].split(':');
                    if(network_split[1])
                        jQuery('.livestream_widget_border .'+network_split[0]+' input').addClass('noauth');
                    else
                        jQuery('.livestream_widget_border .'+network_split[0]+' input').removeClass('noauth');
                }

                jQuery(the_widget).find('img').each(function() {
                    var unsizedsrc = jQuery(this).attr('unsizedsrc');
                    var width = jQuery(this).parent().width();
                    if(width > jQuery(this).attr('maxwidth'))
                        width = jQuery(this).attr('maxwidth');
                    var timthumb = livestream_url+'/view/frontend/timthumb.php?src='+unsizedsrc+'&w='+width;
                    jQuery(this).attr('src', timthumb).show();
                });
            },
            error : function(data) {
                jQuery(the_widget).removeClass('pending');
            }
        });
    });
}

function liveStream_filetype(ext, name) {
    switch(ext) {
        case '.png':
        case '.jpg':
        case '.jpeg':
        case '.bmp':
        case '.gif':
            return 'Image';
        case '.avi':
        case '.mpg':
        case '.flv':
            return 'Video';
    }
    return name;
}

var gfile = null;
var liveStream_swfhandles = {
    uploadStarted : function(file) {
	    try {
            gfile = file;
            jQuery('#'+this.customSettings.button_placeholder_id).show();
            var the_widget = jQuery('#'+this.customSettings.button_placeholder_id).parent();
            var i = 20;
            while((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container')) && i-- > 0)
                the_widget = the_widget.parent();
            jQuery(the_widget).addClass('pending');
            jQuery(the_widget).find('.deattachment-placeholder').hide();    // attaching over the last attachment
	    }
	    catch (ex) {}

	    return true;
    },
    uploadProgress : function(file, bytesLoaded, bytesTotal) {
	    try {
		    var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		    if(percent < 100)
    		    jQuery('#'+this.customSettings.button_placeholder_id).html('Uploading: '+percent+'%');
    		else
    		    jQuery('#'+this.customSettings.button_placeholder_id).html('Processing...');
	    } catch (ex) {
		    this.debug(ex);
	    }
    },
    uploadError : function(file, errorCode, message) {
	    try {
		    switch (errorCode) {
		    case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			    message = ("Upload Error: " + message);
			    this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			    message = ("Upload Failed.");
			    this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			    message = ("Server (IO) Error");
			    this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			    message = ("Security Error");
			    this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			    message = ("Upload limit exceeded.");
			    this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			    message = ("Failed Validation.  Upload skipped.");
			    this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			    break;
		    case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			    message = ("Cancelled");
			    break;
		    case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			    progress.setStatus("Stopped");
			    break;
		    default:
			    message = ("Unhandled Error: " + errorCode);
			    this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			    break;
		    }
		    alert(message);
		    jQuery('#'+this.customSettings.button_placeholder_id).hide();
	    } catch (ex) {
            this.debug(ex);
        }
    },
    uploadSuccess : function(file, server_data, receivedResponse) {
        try {
            jQuery('#'+this.customSettings.button_placeholder_id).prev().val(server_data);
            jQuery('#'+this.customSettings.button_placeholder_id).html('' + liveStream_filetype(file.type, file.name) + " attached");
            jQuery('#'+this.customSettings.button_placeholder_id).parent().find('.deattachment-placeholder').show();
	    } catch (ex) {
            this.debug(ex);
        }
    },
    uploadComplete : function(file) {
        jQuery('#'+this.customSettings.button_placeholder_id).show();
        var the_widget = jQuery('#'+this.customSettings.button_placeholder_id).parent();
        var i = 20;
        while((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container')) && i-- > 0)
            the_widget = the_widget.parent();
        jQuery(the_widget).removeClass('pending');
    },
    queueComplete : function() {
        //alert("queue complete");
    },
    fileQueued : function() {
        //alert("file queued");
    },
    fileQueueError : function(file, errorCode, message) {
        switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			alert('Files cannot be large than '+this.customSettings.sizelimit+'MB.');
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			alert("Cannot upload Zero Byte files.");
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			alert("Invalid File Type.");
			break;
		default:
			break;
		}
    },
    // start upload when file is added
    fileDialogComplete : function(numFilesSelected, numFilesQueued) {
	    try {
		    //if (numFilesSelected > 0) {
			//    document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		    //}
		    this.startUpload();
	    } catch (ex)  {
            this.debug(ex);
	    }
    }
};

function liveStream_widget_from_element(el) {
    var the_widget = jQuery(el).parent();
    var i = 20;
    while((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container')) && i-- > 0)
        the_widget = the_widget.parent();
    if((!the_widget.hasClass('widget') && !the_widget.hasClass('widget-container'))) return false;
    return the_widget;
}
var blob = null;
//
// perform functions on the URL from the textarea
//
function liveStream_urls_from_text(text, textarea) {
    var urlRegex = /((https?:\/\/|www\.)[^\s]+)/g;
    var thewidget = null;
    var external_attached = false;
    return text.replace(urlRegex, function(url) {
        if(!thewidget)
            thewidget = liveStream_widget_from_element(textarea);

        // attach external rich media
        if(!external_attached)
            external_attached = attach_external_media(url, thewidget);

        // shorten url's
        if(url.length > 20) {
            liveStream_bitly_shorten(url);
        }
    });
}

//
// attach external rich media
//
var richmediaurl = [];

function attach_external_media(url, thewidget) {
    var progress = thewidget.find('.swfupload-progress');
    if(thewidget.hasClass('pending'))
        return true; // don't want to mess with an updating widget

    if(progress.is(':visible') && progress.html().length && progress.html().indexOf('Attached') > -1)
        return true; // skip attachment

    var toattach = false;
    // youtube url support
    //if(url.indexOf('http://www.youtube.com/'))
    //    toattach = true;

    var ext = '.'+url.split('.').pop();
    var type = liveStream_filetype(ext, '');
    if(liveStream_filetype(ext, '') != 'Image')
        return false;

    var islocal = '';
    var localurl = livestream_site_url.replace('http://','');
    var checkurl = url.replace('http://','');
    if(checkurl.indexOf(localurl))
        islocal = 'External ';

    thewidget.find('.swfupload-progress').prev().val(url);
    thewidget.find('.swfupload-progress').html(islocal + type + ' Attached!');
    thewidget.find('.swfupload-progress').show();
    thewidget.find('.deattachment-placeholder').show();
}

//
// url shortening section
//
var resultness = null;
function liveStream_init_bitly() {
    jQuery('body')[0].bitly = {
        api_key : null,
        api_url : "api.bitly.com",
        api_login : null
    };

    jQuery.get(livestream_site_url+"/?livestream_bitly=1", function(res) {
        res = jQuery.parseJSON(res);
        jQuery('body')[0].bitly.api_key = res.api_key;
        jQuery('body')[0].bitly.api_login = res.api_login;
    });

    jQuery('.widget_livestream_widget textarea').live('keyup',function(event) {
        if(event.which == 32)
            liveStream_urls_from_text(jQuery(this).val(), this);
    });

    jQuery('.widget_livestream_widget textarea').live('blur',function(event) {
        liveStream_urls_from_text(jQuery(this).val(), this);
    });
}

function liveStream_bitly_callback(callbackreturn) {
    jQuery('.widget_livestream_widget textarea').each(function() {
        if(jQuery(this).val().indexOf(callbackreturn.data.long_url) < 0) return;

        var top = jQuery(this).scrollTop();
        liveStream_replace_textarea_val(this, callbackreturn.data.long_url, callbackreturn.data.url);
        if(callbackreturn.data.long_url[callbackreturn.data.long_url.length-1] == '/')
            callbackreturn.data.long_url = callbackreturn.data.long_url.substr(0, callbackreturn.data.long_url.length-1);
        liveStream_replace_textarea_val(this, callbackreturn.data.long_url, callbackreturn.data.url);
        jQuery(this).scrollTop(top);
    });
}

function liveStream_bitly_shorten(url) {
    var bitly = jQuery('body')[0].bitly;

    if(!bitly.api_key || !bitly.api_login || !bitly.api_url) { return; }

    if(!url || !liveStream_bitly_callback) { throw "Attempt to call shorten without a url or a callback function"; }

    if(url.indexOf('http') != 0)
        url = 'http://' + url;

    jQuery.getJSON("http://"+bitly.api_url+"/v3/shorten?longUrl="+encodeURIComponent(url)+"&login="+bitly.api_login+"&apiKey="+bitly.api_key+"&callback=?", liveStream_bitly_callback);
}

function liveStream_input_length(text) {
    var bitly = jQuery('body')[0].bitly;
    if(!bitly.api_key || !bitly.api_login || !bitly.api_url) { return text.length; }

    var length = text.length;
    var urlRegex = /((https?:\/\/|www\.)[^\s]+)/g;
    text.replace(urlRegex, function(url) {
        // shorten textarea length by howmany units a URL takes up
        if(url.length > 20)
            length -= (url.length - 20);
    });
    return length;
}

function liveStream_input_length_max(text, maxlen) {
    var bitly = jQuery('body')[0].bitly;
    if(!bitly.api_key || !bitly.api_login || !bitly.api_url) { return maxlen; }

    var length = maxlen;
    var urlRegex = /((https?:\/\/|www\.)[^\s]+)/g;
    text.replace(urlRegex, function(url) {
        // shorten textarea length by howmany units a URL takes up
        if(url.length > 20)
            length += (url.length - 20);
    });
    return length;
}

// when replacing text in textarea rest the cursor so we don't annoy the author
function liveStream_replace_textarea_val(textarea, from, to) {
    var diff = from.length - to.length;
    var new_value = textarea.value;
    var new_value_parts = new_value.split(from);
    var sel = liveStream_getInputSelection(textarea);

    // find new cursor start
    var pos = 0;
    for(i = 0; i < new_value_parts.length; ++i) {
        pos += new_value_parts[i].length;
        if(pos < sel.start) {
            sel.start = sel.start - diff;
        } else break;
        pos += new_value.length;
    }

    // find new cursor start
    pos = 0;
    for(i = 0; i < new_value_parts.length; ++i) {
        pos += new_value_parts[i].length;
        if(pos < sel.end) {
            sel.end = sel.end - diff;
        } else break;
        pos += new_value.length;
    }

    textarea.value = textarea.value.replace(from, to);
    liveStream_setInputSelection(textarea, sel.start, sel.end);
}

function liveStream_getInputSelection(el) {
    var start = 0, end = 0, normalizedValue, range,
        textInputRange, len, endRange;

    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        start = el.selectionStart;
        end = el.selectionEnd;
    } else {
        range = document.selection.createRange();

        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }
    }

    return {
        start: start,
        end: end
    };
}

function liveStream_offsetToRangeCharacterMove(el, offset) {
    return offset - (el.value.slice(0, offset).split("\r\n").length - 1);
}

function liveStream_setInputSelection(el, startOffset, endOffset) {
    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        el.selectionStart = startOffset;
        el.selectionEnd = endOffset;
    } else {
        var range = el.createTextRange();
        var startCharMove = liveStream_offsetToRangeCharacterMove(el, startOffset);
        range.collapse(true);
        if (startOffset == endOffset) {
            range.move("character", startCharMove);
        } else {
            range.moveEnd("character", liveStream_offsetToRangeCharacterMove(el, endOffset));
            range.moveStart("character", startCharMove);
        }
        range.select();
    }
}

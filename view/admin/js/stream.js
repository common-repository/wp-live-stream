

jQuery(document).ready(function(){
    if(livestream_max_lengths.default.length && parseInt(livestream_max_lengths.default) > 0) {
        jQuery('#title-prompt-text').html('Enter '+livestream_max_lengths.default+' characters or less');
        jQuery('input[name=post_title]').bind('change keyup', function() {
            if(liveStream_input_length(jQuery(this).val()) > parseInt(livestream_max_lengths.default))
                jQuery(this).val(jQuery(this).val().substr(0, liveStream_input_length_max(jQuery(this).val(),parseInt(livestream_max_lengths.default))));
        });
    } else
        jQuery('#title-prompt-text').html('Enter your Live Stream feed');
    
    jQuery('body').on('mouseover', '.widget-content', reset_farbtastic);
    
    jQuery('.farbtastic-input').live('focus', function(){
        var $this = jQuery(this);
        jQuery('div[rel='+$this.attr('id')+']').slideDown();
    });
    jQuery('.farbtastic-input').live('blur', function(){
        var $this = jQuery(this);
        jQuery('div[rel='+$this.attr('id')+']').slideUp();
    });
    
    liveStream_init_bitly();
    
    if(jQuery('.nav-tab-wrapper').length && window.location.hash.toString().length) {
        var tab = window.location.hash.toString().replace("#",'');
        jQuery('.nav-tab').removeClass('nav-tab-active');
        jQuery('#'+tab).addClass('nav-tab-active');
        jQuery('.tab').hide();
        jQuery('#tab-'+tab).show();
    }
});

var liveStream_hexd = new Array
        ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

//Function to convert hex format to a rgb color
function liveStream_rgb2hex(rgb) {
 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
 return "#" + liveStream_hex(rgb[1]) + liveStream_hex(rgb[2]) + liveStream_hex(rgb[3]);
}

function liveStream_hex(x) {
  return isNaN(x) ? "00" : liveStream_hexd[(x - x % 16) / 16] + liveStream_hexd[x % 16];
}

function reset_farbtastic() {    
    jQuery('.farbtastic-picker').each(function(){
        var $this = jQuery(this),
            id = $this.attr('rel');
            
        if($this.hasClass('farb') || !$this.is(':visible')) return;
        
        $this.farbtastic('#' + id);
        $this.hide();
        
        $this.addClass('farb');
    });
}

// admin tabs

function liveStream_tab(tab) {
    var id = jQuery(tab).attr('id');
    jQuery('.nav-tab').removeClass('nav-tab-active');
    jQuery(tab).addClass('nav-tab-active');
    jQuery('.tab').hide();
    jQuery('#tab-'+id).show();
    return true;
}

// url shortners
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
    
    jQuery('input[name=post_title]').live('keyup',function(event) {
        if(event.which == 32)
            liveStream_shorten_urls_from_text(jQuery(this).val());
    });
    
    jQuery('input[name=post_title]').live('blur',function(event) {
        liveStream_shorten_urls_from_text(jQuery(this).val());
    });
}

function liveStream_bitly_callback(callbackreturn) {
    jQuery('input[name=post_title]').each(function() {
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

function liveStream_shorten_urls_from_text(text) {    
    var urlRegex = /((https?:\/\/|www\.)[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        // shorten url's
        if(url.length > 20)
            liveStream_bitly_shorten(url);
    })
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


function liveStream_input_length_max(text,maxlen) {
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

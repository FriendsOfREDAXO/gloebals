var gloebals_textareawithtabs = {
    /* This has to be updated so we can use TABS in code textareas more comfortably */
    init : function()
    {

        $(document).on({
            'keydown.gloebals' : function(e)
            {
                switch(e.keyCode)
                {
                    case 9 :
                        if(e.shiftKey)
                        {
                            gloebals_textareawithtabs.jumpToPreviousTab(this);
                        }
                        else
                        {
                            gloebals_textareawithtabs.insertTextAtCursor(this, "  ");
                        }
                        return false;
                        break;
                    case 13 :
                        // return gloebals_textareawithtabs.insertLinebreakAtCursor(this);
                        break;
                }
            }
        }, 'textarea.gloebals--code');
    },

    insertLinebreakAtCursor : function (el) {
        return true;
        var val = el.value, endIndex, range;
        if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined")
        {
            before = val.slice(0, el.selectionStart);
            matches = before.match(/(\n|^)(\t+|\s+)?[^\n]+$/);
            console.log(matches);
            if(matches && typeof(matches[2]) != 'undefined')
            {
                this.insertTextAtCursor(el, "\n" + matches[2]);
                return false;
            }
        }
        else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined")
        {
            el.focus();
            range = document.selection.createRange();
            range.collapse(false);
            console.log("RANGE");
            console.log(range);
            range.select();
        }

        return true;
    },

    jumpToPreviousTab : function(el)
    {
        var val = el.value, endIndex, range;
        if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined")
        {
            before = val.slice(0, el.selectionStart);

            matches = before.match(/(\n|^)(.*)[^\n]+$/);
            console.log(matches);
        }
        else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined")
        {
            el.focus();
            range = document.selection.createRange();
            range.collapse(false);
            console.log("RANGE");
            console.log(range);
            range.select();
        }
    },

    insertTextAtCursor : function (el, text) {
        var val = el.value, endIndex, range;
        if (typeof el.selectionStart != "undefined" && typeof el.selectionEnd != "undefined")
        {
            endIndex = el.selectionEnd;
            el.value = val.slice(0, el.selectionStart) + text + val.slice(endIndex);
            el.selectionStart = el.selectionEnd = endIndex + text.length;
        }
        else if (typeof document.selection != "undefined" && typeof document.selection.createRange != "undefined")
        {
            el.focus();
            range = document.selection.createRange();
            range.collapse(false);
            range.text = text;
            range.select();
        }
    }
}

$(document).on('ready.gloebals', $.proxy(gloebals_textareawithtabs.init, gloebals_textareawithtabs));

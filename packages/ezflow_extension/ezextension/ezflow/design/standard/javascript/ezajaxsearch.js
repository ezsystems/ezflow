var eZAJAXSearch = function()
{
    var ret = {};

    var yCallback = function(Y, result)
    {
        var successCallBack = function(id, o)
        {
            if (o.responseJSON !== undefined)
            {
                var response = o.responseJSON;

                if (response.content.SearchResult !== undefined)
                {
                    var itemCount = response.content.SearchResult.length;

                    var resultsTarget = Y.get(ret.cfg.searchresults);
                    resultsTarget.set('innerHTML', '');
                    resultsTarget.addClass('loading');

                    for(var i = 0; i < itemCount; i++)
                    {
                        var item = response.content.SearchResult[i];

                        var template = ret.cfg.resulttemplate;
                        template = template.replace(/\{+title+\}/, item.name);
                        template = template.replace(/\{+date+\}/, item.published_date);
                        template = template.replace(/\{+class_name+\}/, item.class_name);
                        template = template.replace(/\{+url_alias+\}/, item.url_alias);
                        template = template.replace(/\{+object_id+\}/, item.id);

                        var itemContainer = Y.Node.create(template);

                        resultsTarget.removeClass('loading');
                        resultsTarget.appendChild(itemContainer);
                    }
                }
            }
        }

        var getValueForSelector = function(sel)
        {
            var value, node = Y.one(sel);

            if ( node )
            {
                if (node.get('nodeName').toLowerCase() == 'input'
                        && (node.get('type') == 'radio' 
                                || node.get('type') == 'checkbox'))
                {
                    value = (Y.one(sel + ':checked') != null) ? Y.one(sel + ':checked').get('value') : null;
                }
                else if (node.get('nodeName').toLowerCase() == 'select'
                                && node.hasAttribute('multiple'))
                {
                    value = [];

                    for (var i = 0; i < node.get('options').size(); i++)
                    {
                        var option = node.get('options').item(i);
                        if (option.get('selected'))
                            value.push(option.get('value'));
                    }

                    value = value.join(',');
                }
                else
                {
                    value = node.get('value');
                }
            }

            return value;
        }

        var performSearch = function()
        {
            var searchString = getValueForSelector(ret.cfg.searchstring);
            var dateFormatType = ret.cfg.dateformattype;

            var value, data = 'SearchStr=' + searchString;
            data += '&SearchLimit=' + getValueForSelector('[name=SearchLimit]');

            if (value = getValueForSelector('[name=SearchOffset]'))
                data += '&SearchOffset=' + value;

            if (value = getValueForSelector('[name=SearchSectionID]'))
                data += '&SearchSectionID=' + value;

            if (value = getValueForSelector('[name=SearchDate]'))
                data += '&SearchDate=' +  value;

            if (value = getValueForSelector('[name=SearchContentClassAttributeID]'))
                data += '&SearchContentClassAttributeID=' + value;

            if (value = getValueForSelector('[name=SearchContentClassID]'))
                data += '&SearchContentClassID=' + value;

            if (value = getValueForSelector('[name=SearchContentClassIdentifier]'))
                data += '&SearchContentClassIdentifier=' + value;

            if (value = getValueForSelector('[name=SearchSubTreeArray]'))
                data += '&SearchSubTreeArray=' + value;

            if (value = getValueForSelector('[name=SearchTimestamp]'))
                data += '&SearchTimestamp=' + value;

            data += '&EncodingFormatDate=' + dateFormatType;

            var backendUri = ret.cfg.backendUri ? ret.cfg.backendUri : 'ezjsc::search' ;

            if(searchString !== '')
            {
                Y.io.ez(backendUri, {on: {success: successCallBack}, method: 'POST', data: data });
            }
        }

        var handleClick = function(e)
        {
            performSearch();
            e.preventDefault();
        }

        var handleKeyPress = function(e)
        {
            var key = e.which || e.keyCode;
            if (key == 13)
            {
                performSearch();
                e.halt();
            }
        }

        Y.get(ret.cfg.searchbutton).on('click', handleClick);
        Y.get(ret.cfg.searchstring).on('keypress', handleKeyPress);
    }
    ret.cfg = {};

    ret.init = function()
    {
        var ins = YUI(YUI3_config).use('node', 'event', 'io-ez', yCallback);
    }
    
    return ret;
}();
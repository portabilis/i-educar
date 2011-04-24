function getText(e)
{
    var t = '';
    if (e)
    {
        if (e.childNodes)
        {
            for (var i = 0; i < e.childNodes.length; i++)
            {
                var child = e.childNodes[i];
                if (child.nodeValue)
                    t += child.nodeValue;
                else
                {
                    if (child.childNodes[0])
                        if (child.childNodes[0].nodeValue)
                            t += child.childNodes[0].nodeValue;
                }
            }
        }
    }
    return t;
}

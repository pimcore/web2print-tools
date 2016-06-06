/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


// generate a table of contents for all H2
createTOC = function() {
    var toc = '';
    // iterate through the headings
    var headings = document.querySelectorAll("h1, h2, h3, h4");
    for(var i = 0; i < headings.length; i++) {
        // wrap the content into an anchor with a generated ID
        var a = document.createElement("a");
        a.id = 'generatedTOCtarget' + i;
        var children = headings[i].childNodes;
        for(var j = children.length - 1; j > -1; j--) {
            a.insertBefore(children[j], a.firstChild);
        }
        headings[i].appendChild(a);
        // add line to TOC HTML
        toc += '<div class="target' + headings[i].nodeName.toUpperCase() + '"><a href="#' + a.id + '">' + a.textContent + '</a></div>'
    }
    // wrap TOC HTML into container
    toc = ''
        + '<div class="generatedTOC">'
        + (document.title ? '<h2>' + document.title + '</h2>' : '')
        + '<h2>Table of Contents</h2>'
        + toc + '</div>';
    // insert TOC HTML before the content of BODY
    //document.body.insertAdjacentHTML("afterbegin", toc);

    $('#toc').html(toc);

    // workaround to update page headers
    document.body.offsetWidth;
}
createTOC();
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


/**
 * Creates a table of contents into the passed 'insertiontarget' container tag.
 * 
 * - include bundles/web2printtools/vendor/js/awesomizr.js
 * - include bundles/web2printtools/vendor/css/awesomizr.css
 *
 * Make sure css variable "page" is set in your script to get the page numbers
 * for the toc.
 * 
 *
 */

Awesomizr.createTableOfContents({
    /* toc container */
    insertiontarget: '#toc-wrapper',
    /* levels to look for and link to in toc*/
    elements: ['.toc-level-1','.toc-level-2'],
    /* container element for the toc */
    container: {tag: 'ul', addClass: 'toc'},
    /* container element for one line in the toc */
    line: {tag: 'li'},
    disabledocumenttitle: true,
    toctitle: ' ',
    /* method of getting the text for the toc lines */
    text: function (elem) {
        var txt = elem.title;

        if (txt) {
            return txt;
        }

        return elem.textContent;
    }
});
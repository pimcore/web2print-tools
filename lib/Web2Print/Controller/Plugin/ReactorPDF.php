<?php
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


namespace Web2Print\Controller\Plugin;


use Pimcore\Config;

class ReactorPDF extends \Zend_Controller_Plugin_Abstract {

    public function __construct($overlay = null, $filename = "export.pdf") {
        $this->overlay = $overlay;
        $this->filename = $filename;
    }

    public function dispatchLoopShutdown() {

        if(!\Pimcore\Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        $body = $this->getResponse()->getBody();

        $this->createPDF($body);
    }

    public function createPDF($html, $outputPdf = true) {

        file_put_contents(PIMCORE_TEMPORARY_DIRECTORY . "/pdf-reactor-input.html", $html);
        $html = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $html);

        if(!$_GET['html']) {
            $result = $this->doCreatePDF8($html);

            if($outputPdf) {
                // Set the correct header for PDF output and echo PDF content
                header("Content-Type: application/pdf");
                header("Content-Disposition: inline; filename=\"{$this->filename}\";");
                header("Cache-Control: private", true);
                header_remove("Pragma");
                echo $result;
            } else {
                return $result;
            }

            exit;
        }
    }


    protected function doCreatePDF8($html) {
        $web2PrintConfig = Config::getWeb2PrintConfig();
        include_once(PIMCORE_PATH . '/lib/Pimcore/Web2Print/Processor/api/PDFreactor.class.php');

        $port = ((string) $web2PrintConfig->pdfreactorServerPort) ? (string) $web2PrintConfig->pdfreactorServerPort : "9423";
        $pdfreactor = new \PDFreactor("http://" . $web2PrintConfig->pdfreactorServer . ":" . $port . "/service/rest");

        $reactorConfig = [
            "document" => $html,
            "baseURL" => (string) $web2PrintConfig->pdfreactorBaseUrl,
            "addLinks" => true,
            "addBookmarks" => true,
            "javaScriptMode" => \JavaScriptMode::ENABLED_NO_LAYOUT
        ];

        if (trim($web2PrintConfig->pdfreactorLicence)) {
            $reactorConfig["licenseKey"] = trim($web2PrintConfig->pdfreactorLicence);
        }

        try {
            $result = $pdfreactor->convert($reactorConfig);
            $pdf = base64_decode($result->document);
            return $pdf;
        } catch(\Exception $e) {
            echo "Error during rendering: <br/>".$e->getMessage();
            return "";
        }
    }
}

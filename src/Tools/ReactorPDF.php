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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Web2PrintToolsBundle\Tools;

use Pimcore\Config;
use Symfony\Component\HttpFoundation\Response;

class ReactorPDF {

    /**
     * @var string
     */
    protected $overlay;

    /**
     * @var string
     */
    protected $filename;

    public function __construct($overlay = null, $filename = "export.pdf") {
        $this->overlay = $overlay;
        $this->filename = $filename;
    }

    /**
     * @param $html string
     * @return Response
     */
    public function createPDFResponse($html) {

        file_put_contents(PIMCORE_TEMPORARY_DIRECTORY . "/pdf-reactor-input.html", $html);
        $html = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $html);

        if(!$_GET['html']) {
            $result = $this->doCreatePDF8($html);

            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=" . ' . $this->filename . '";"',
                'Cache-Control' => 'private'
            ];

            return new Response($result, 200, $headers);

        } else {
            return new Response($html);
        }
    }


    protected function doCreatePDF8($html) {
        $web2PrintConfig = Config::getWeb2PrintConfig();
        include_once('Pimcore/Web2Print/Processor/api/v' . $web2PrintConfig->get('pdfreactorVersion', '8.0') . '/PDFreactor.class.php');

        $port = ((string) $web2PrintConfig->pdfreactorServerPort) ? (string) $web2PrintConfig->pdfreactorServerPort : "9423";
        $protocol = ((string) $web2PrintConfig->pdfreactorProtocol) ? (string) $web2PrintConfig->pdfreactorProtocol : "http";

        $pdfreactor = new \PDFreactor($protocol . "://" . $web2PrintConfig->pdfreactorServer . ":" . $port . "/service/rest");

        $reactorConfig = [
            "document" => $html,
            "baseURL" => (string) $web2PrintConfig->pdfreactorBaseUrl,
            "addLinks" => true,
            "addBookmarks" => true,
            "javaScriptMode" => \JavaScriptMode::ENABLED_NO_LAYOUT
        ];

        if($this->overlay) {
            $reactorConfig['mergeURL'] = (string) $web2PrintConfig->pdfreactorBaseUrl . $this->overlay;
            $reactorConfig['mergeMode'] = \MergeMode::OVERLAY;
            $reactorConfig['overlayRepeat'] = \OverlayRepeat::ALL_PAGES;
        }

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

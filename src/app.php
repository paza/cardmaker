<?php

define('tFPDF_FONTPATH','tfpdf/font/');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/CardMaker.php';

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Igorw\Trashbin\Storage;
use Igorw\Trashbin\Validator;
use Igorw\Trashbin\Parser;

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

use Symfony\Component\Finder\Finder;
use Paza\CardMaker;

$app = new Application();

$app['debug'] = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
    'twig.options' => array('cache' => __DIR__.'/../cache/twig', 'debug' => true),
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Front page
 */
$app->match('/', function(Request $request) use($app) {

    return $app['twig']->render('front.html.twig', array(

    ));

})
->bind('home');

/**
 * Cards PDF
 */
$app->match('/cards.pdf', function(Request $request) use($app) {

    $cardsString    = $request->get('cards');
    $cardsPerPage   = $request->get('cardsPerPage', 8);
    $showNumbers    = $request->get('showNumbers') == 1 ? true : false;
    $cropmark       = $request->get('cropmark') == 1 ? true : false;
    $alignment      = $request->get('alignment', 'L');
    $splitter       = "\n"; // default splitter is \n
    $orientation    = "L"; // default orientation is L

    // check if cards is not empty
    if (empty($cardsString)) {
        return $app->redirect($app['url_generator']->generate('home'));
    }

    // detect delimiter "---"
    if(strpos($cardsString, "---") !== false) {
        $splitter = "---";
    }

    // split card string
    $cards = explode($splitter, trim($cardsString));

    // set orientation
    if ($cardsPerPage == 8 || $cardsPerPage == 2) {
        $orientation = "P";
    }

    // create a new PDF
    $pdf = new tFPDF($orientation, "mm", "A4");

    // add DejaVu font to PDF
    $pdf->AddFont('DejaVu','', 'DejaVuSans.ttf', true);
    $pdf->AddFont('DejaVuBold', '', 'DejaVuSans-Bold.ttf', true);

    // set margins, colour and line width
    $pdf->SetTopMargin = 0;
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetLineWidth(0.1);

    // get cardmaker by cards per page
    switch ($cardsPerPage){
        case 1:
            $cardMaker = new CardMaker\CardMaker($pdf);
            break;
        case 2:
            $cardMaker = new CardMaker\CardMakerTwo($pdf);
            break;
        case 4:
            $cardMaker = new CardMaker\CardMakerFour($pdf);
            break;
        case 8:
            $cardMaker = new CardMaker\CardMakerEight($pdf);
            break;
    }

    // create PDF
    $cardMaker->createCardPdf($cards, $showNumbers, $cropmark, $alignment);

    // output PDF
    return $pdf->Output("Cardsortdeck.pdf", "D");
})
->bind('cards.pdf');

return $app;


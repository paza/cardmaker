<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

    // get user input
    $cardsString    = $request->get('cards');
    $cardsPerPage   = $request->get('cardsPerPage', 8);
    $showNumbers    = $request->get('showNumbers') == 1 ? true : false;
    $cropmark       = $request->get('cropmark') == 1 ? true : false;
    $alignment      = $request->get('alignment', 'L');
    $html           = $request->get('html') == 1 ? true : false;

    // set defaults
    $splitter       = "\n"; // default splitter is \n
    $orientation    = "L"; // default orientation is L
    $template       = 'cards_single_column.html.twig';
    $splitColumns   = 1;
    $splitPages     = $cardsPerPage;

    // check if cards is not empty
    if (empty($cardsString)) {
        return $app->redirect($app['url_generator']->generate('home'));
    }

    // remove disallowed tags
    if (true === $html) {
        $cardsString = strip_tags($cardsString, '<h2><h3><p><br><b><i>');
    }

    // detect delimiter "---"
    if(strpos($cardsString, "---") !== false) {
        $splitter = "---";
    }

    // split card string
    $cards = explode($splitter, trim($cardsString));

    // set card numbers
    foreach ($cards as $key => &$card) {
        $card = array(
            'text' => trim($card),
            'number' => ($key + 1),
        );
    }

    // set orientation
    if (in_array($cardsPerPage, array(2,8))) {
        $orientation = "P";
    }

    // set params to display 4 or 8 cards per page
    if ($cardsPerPage >= 4) {
        $template = 'cards_two_columns.html.twig';
        $splitColumns = 2; // chunk to two per row
        $splitPages = $cardsPerPage / 2; // chunk to pages (4 or 8 per page)
    }

    // chunk array to be rendered in the twig template
    $rows = array_chunk($cards, $splitColumns);
    $pages = array_chunk($rows, $splitPages);

    // render twig template
    $content = $app['twig']->render($template, array(
        'pages'         => $pages,
        'cardsPerPage'  => $cardsPerPage,
        'html'          => $html,
        'cropmark'      => $cropmark,
        'alignment'     => $alignment,
        'showNumbers'   => $showNumbers,
    ));

    // init HTML2PDF
    $html2pdf = new HTML2PDF($orientation, 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));

    // display the full page
    $html2pdf->pdf->SetDisplayMode('fullpage');

    // convert
    $html2pdf->writeHTML($content, false);

    // send the PDF
    $html2pdf->Output('about.pdf');
    exit();

})
->bind('cards.pdf');

return $app;


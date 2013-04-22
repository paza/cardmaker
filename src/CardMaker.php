<?php

namespace Paza\CardMaker;

/**
 * One card per page
 */
class CardMaker
{
    protected $pdf = NULL;

    // number of cards per page
    protected $cardsPerPage = 1;

    // number properties
    protected $numberFontSize = 20;
    protected $numberPositionX = 240;
    protected $numberPositionY = 20;

    // Text properties
    protected $textFontSize = 32;
    protected $textWidth = 257;
    protected $textHeight = 16;

    // XY Mapping for the card positioning
    protected $xyMapping = array(
        array(20, 30),
    );

    public function __construct($pdf) {
        $this->pdf = $pdf;
    }

    /**
     * Create a PDF from an array of strings
     *
     * @param array $strings
     * @param boolean $showNumbers
     * @param boolean $drawCropmarks
     * @param string $alignment
     * @return void
     */
    public function createCardPdf($strings, $showNumbers, $drawCropmarks, $alignment = "L") {

        foreach ($strings as $key => $string) {
            $string = trim($string);

            // add page if necessary
            if (0 === $key % $this->cardsPerPage) {
                $this->addPage();

                if ($drawCropmarks) {
                    $this->drawCropmarks();
                }
            }

            $this->setXY($key);

            // add numbers
            if (true === $showNumbers) {
                $this->showNumbers($key + 1);
            }

            $this->writeCardName($string, $alignment);
        }
    }

    /**
     * Add a new page
     *
     * @param int $key
     * @return void
     */
    protected function addPage() {
        $this->pdf->AddPage();
    }

    /**
     * Add card numbers
     *
     * @param int $num
     * @return void
     */
    protected function showNumbers($num) {
        $this->pdf->SetFont('DejaVu', '', $this->numberFontSize);
        $this->pdf->Cell($this->numberPositionX, $this->numberPositionY, (string) $num, 0, 2);
    }

    /**
     * Write card text
     *
     * @param string $string
     * @return void
     */
    protected function writeCardName($string, $alignment) {
        $this->pdf->SetFont('DejaVuBold', '', $this->textFontSize);
        $this->pdf->MultiCell( $this->textWidth, $this->textHeight, $string, 0, $alignment); // write Cardname
        // Syntax: MultiCell(float w , float h , string txt [, mixed border] [, string align] [, integer fill])
    }

    /**
     * Draws cropmarks
     *
     * @return void
     */
    protected function drawCropmarks() {
        // draw line here
    }

    /**
     * Sets the positions for the card
     *
     * @param int $key
     * @return void
     */
    protected function setXY($key) {
        $number = $key % $this->cardsPerPage;
        $this->pdf->SetXY($this->xyMapping[$number][0], $this->xyMapping[$number][1]);
    }

}

/**
 * Two cards per page
 */
class CardMakerTwo extends CardMaker
{
    // number of cards per page
    protected $cardsPerPage = 2;

    // number properties
    protected $numberFontSize = 18;
    protected $numberPositionX = 110;
    protected $numberPositionY = 10;

    // Text properties
    protected $textFontSize = 20;
    protected $textWidth = 180;
    protected $textHeight = 8;

    // XY Mapping for the card positioning
    protected $xyMapping = array(
        array(20, 20),
        array(20, 168),
    );

    /**
     * Draws cropmarks
     *
     * @return void
     */
    protected function drawCropmarks() {
        $this->pdf->Line(0, 148, 20, 148);
        $this->pdf->Line(190, 148, 210,148);
    }
}

/**
 * Four cards per page
 */
class CardMakerFour extends CardMaker
{
    // number of cards per page
    protected $cardsPerPage = 4;

    // number properties
    protected $numberFontSize = 12;
    protected $numberPositionX = 110;
    protected $numberPositionY = 10;

    // Text properties
    protected $textFontSize = 14;
    protected $textWidth = 110;
    protected $textHeight = 8;

    // XY Mapping for the card positioning
    protected $xyMapping = array(
        array(20, 30),
        array(170, 30),
        array(20, 125),
        array(170, 125),
    );

    /**
     * Draws cropmarks
     *
     * @return void
     */
    protected function drawCropmarks() {
        $this->pdf->Line(148, 0, 148, 20);
        $this->pdf->Line(148, 190, 148, 210);
        $this->pdf->Line(0, 105, 20, 105);
        $this->pdf->Line(277, 105, 297, 105);
    }
}

/**
 * Eight cards per page
 */
class CardMakerEight extends CardMaker
{
    // number of cards per page
    protected $cardsPerPage = 8;

    // number properties
    protected $numberFontSize = 12;
    protected $numberPositionX = 85;
    protected $numberPositionY = 10;

    // Text properties
    protected $textFontSize = 14;
    protected $textWidth = 85;
    protected $textHeight = 7;

    // XY Mapping for the card positioning
    protected $xyMapping = array(
        array(10, 10),
        array(115, 10),
        array(10, 82),
        array(115, 82),
        array(10, 154),
        array(115, 154),
        array(10, 226),
        array(115, 226),
    );

    /**
     * Draws cropmarks
     *
     * @return void
     */
    protected function drawCropmarks() {
        $this->pdf->Line(0, 74, 210, 74);
        $this->pdf->Line(0, 148, 210, 148);
        $this->pdf->Line(0, 222, 210, 222);
        $this->pdf->Line(105, 0, 105, 297);
    }
}

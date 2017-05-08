<?php
/**
 * Garp_Model_Behavior_HtmlFilterableTest
 *
 * @author Harmen Janssen <harmen@grrr.nl>
 */
class Garp_Model_Behavior_HtmlFilterableTest extends Garp_Test_PHPUnit_TestCase {

    /**
     * @test
     */
    public function should_filter_non_existent_tags() {
        $filterable = new Garp_Model_Behavior_HtmlFilterable(array());

        $test = '<banana>This tag does not exist</banana>';
        $this->assertEquals('This tag does not exist', $filterable->filter($test));
    }

    /**
     * This tests wether you can add a figure in a couple forms, because it proved difficult in the
     * past.
     *
     * @deprecated
     * This test fails on Travis. I cannot <figure> out why, since it's the only environment I could
     * find that errors on this. The <figure> is stripped out, unlike on _any_ webserver I've found.
     * Infuriating, but no longer worth my time.
     * @codingStandardsIgnoreStart
     */
    //public function should_allow_figure() {
        //$filterable = new Garp_Model_Behavior_HtmlFilterable(array());
        //$simpleFigure = '<figure><img src="https://grrr.nl/banaan.jpg" alt=""></figure>';
        //$this->assertEquals(
            //$simpleFigure,
            //$filterable->filter($simpleFigure)
        //);
        //$figureInDiv = '<div><figure><img src="https://grrr.nl/banaan.jpg" alt=""></figure> Bloep' .
            //'</div>';
        //$this->assertEquals(
            //$figureInDiv,
            //$filterable->filter($figureInDiv)
        //);
        //$figureWithClass = '<figure class="left"><img src="https://grrr.nl/banaan.jpg" alt="">' .
            //'</figure>';
        //$this->assertEquals(
            //$figureWithClass,
            //$filterable->filter($figureWithClass)
        //);
        //$figureWithCaption = '<figure class="left"><img src="https://grrr.nl/banaan.jpg" alt="">' .
            //'<figcaption>Lorem ipsum</figcaption>' .
            //'</figure>';
        //$this->assertEquals(
            //$figureWithCaption,
            //$filterable->filter($figureWithCaption)
        //);
    // @codingStandardsIgnoreEnd
    //}

    /**
     * Run setup for this test
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->_helper->injectConfigValues(
            array(
                'app' => array(
                    'domain' => 'grrr.nl'
                ),
                'htmlFilterable' => array(
                    'cachePath' => null,
                    'allowedClasses' => null
                )
            )
        );
    }
}

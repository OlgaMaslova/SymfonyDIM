<?php
namespace AppBundle\ShowFinder;

interface ShowFinderInterface

{
    /*
     * Returns an array of shows according to the query passed.
     * @param: string  - the query typed by the user.
     *
     * @return: array - the results got from the implementation of the ShowFinder
     */
    public function findByName($query);

    /*
     * Returns the name of the implementation of the ShowFinder
     * @return: string.
     */

    public function getName();

}
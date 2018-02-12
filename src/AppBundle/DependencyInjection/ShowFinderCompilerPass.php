<?php
namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use AppBundle\ShowFinder\ShowFinder;
use Symfony\Component\DependencyInjection\Reference;

class ShowFinderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //Get the definition of the service ShowFinder container in order to add tagged services to it below
        $showFinderDefinition = $container->findDefinition(ShowFinder::class);

        //Get all names of the services under tag 'show.finder'
        $showFinderTaggedServices = $container->findTaggedServiceIds('show.finder');

        //Looking in all services tagged 'show.finder'
        foreach($showFinderTaggedServices as $showFinderTaggedServiceId => $showFinderTags)
        {
            //create a reference (representation of a service) with the id of the tagged service
            $serviceReference = new Reference($showFinderTaggedServiceId);

            //Call 'addFinder' method of the ShowFinder service in order to inject the tagged service (either DBShowFinder or OMDBShowFinder)
            $showFinderDefinition->addMethodCall('addFinder', [$serviceReference]); //dynamic injection of service
        }
    }
}
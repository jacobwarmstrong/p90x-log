<?php
//The Map view uses the google maps dependency and sets up our map view with our app specific content.
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Overlay\Animation;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Overlay\MarkerShape;
use Ivory\GoogleMap\Overlay\MarkerShapeType;
use Ivory\GoogleMap\Overlay\Symbol;
use Ivory\GoogleMap\Overlay\SymbolPath;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Overlay\InfoWindowType;
use Ivory\GoogleMap\Base\Point;

class MapView
{
    /*
    --------------------
    PROPERTIES
    --------------------
    */
    public $className;
    public $addresses =[];
    public $coordinates = [];
    public $bounds;
    
    /*
    CONSTRUCTOR METHOD
    */
    function __construct() 
    {

    }
    
    /*
    -----------------------
    CUSTOM CONSTRUCTOR METHODS
    -----------------------
    */
    public static function render($className, $filters = null) 
    {   
        //intialize object
        $instance = new self();
        $columns = ['projectId', 'projectName', 'streetAddress', 'city', 'state', 'zip'];
        $projects = Database::do('SELECT', 'projects', $columns, 'LIMIT 30');
        $projectsWithCoordinates = $instance->getProjectsArrayWithCoodinates($projects);
        $instance->setBoundForMap($projectsWithCoordinates);

        
        $map = new Map();
        //we set the bounds of the canvas
        $map->setAutoZoom(true);
        
        foreach($projectsWithCoordinates as $project) {
            $marker = new Marker(
                new Coordinate($project['coordinates']->lat,$project['coordinates']->lng),
                Animation::DROP,
                null,
                new Symbol(SymbolPath::CIRCLE),
                new MarkerShape(MarkerShapeType::CIRCLE, [0, 0, 30]),
                ['clickable' => false]
            );
            
            //symbol NOT WORKING
            /*$symbol = new Symbol(
                SymbolPath::CIRCLE,
                //BASE_URL . 'assets/si-glyph-rocket.svg',
                new Point(20, 34),
                new Point(0, 0),
                ['scale' => 10]
            );
            $marker->setSymbol($symbol);*/
            $map->getOverlayManager()->addMarker($marker);
            


            //info window .. make this clickable ..
            /*$infoWindow = new InfoWindow($project['projectName'], InfoWindowType::DEFAULT_, new Coordinate($project['coordinates']->lat,$project['coordinates']->lng));
            $infoWindow->setOpen(true);
            $map->getOverlayManager()->addInfoWindow($infoWindow);*/
        }
        

        //$map->setCenter(new Coordinate($myCoordinates->lat,$myCoordinates->lng));
        $map->setBound($instance->setBoundForMap($projectsWithCoordinates,.001));

        $mapHelper = MapHelperBuilder::create()->build();
        $apiHelper = ApiHelperBuilder::create()
            ->setKey('AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck')
            ->build();
        echo $mapHelper->render($map);
        echo $apiHelper->render([$map]);
        return $instance;
    }
    
    //methods
    private function setBoundForMap(Array $projects, Float $offset = 1.0) {
        //get all project coordinates
        $projects;
        //get all latitudes and longitudes in their respective arrays
        $latArray = [];
        $lngArray = [];
        foreach($projects as $project){
            array_push($latArray, $project['coordinates']->lat);
            array_push($lngArray, $project['coordinates']->lng);
        }
        //find the mins and maxes
        $north = max($latArray);
        $south = min($latArray);
        $west = min($lngArray);
        $east = max($lngArray);
        //assign the west and south to min coordinate
        //assign the north and east to max coordinate
        $west = $west - $offset;
        $south = $south - $offset;
        $east = $east + $offset;
        $north = $north + $offset;
        
        $bound = new Bound( new Coordinate($south, $west), new Coordinate($north, $east) );
        return $bound;
    }
    
    public function getProjectsArrayWithCoodinates(array $projects) 
    {
        for($i=0;$i<count($projects);$i++) {
            $address = $this->getAddress(
                $projects[$i]['streetAddress'],
                $projects[$i]['city'],
                $projects[$i]['state'],
                $projects[$i]['zip']
            );
            $projects[$i]['address'] = $address;
            $projects[$i]['coordinates'] = $this->getCoordinates($projects[$i]['address']);
            echo $projects[$i]['projectId'] . ' coord: ' . $projects[$i]['coordinates']->lat . $projects[$i]['coordinates']->lng . '<br>';
        }
        return $projects;
    }
    
    public function getAddress($street, $city, $state, $zip) {
        return "{$street}, {$city}, {$state}, {$zip}";
    }
    
    
    private function getCoordinates($address) 
    {
       $addressPrep = str_replace (" ", "+", urlencode($address));
       $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $addressPrep . "&key=AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck";

        $geocode = file_get_contents($details_url);
        $response = json_decode($geocode);
        //var_dump($response->results[0]->geometry);
        $coordinates = $response->results[0]->geometry->location;

       // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
       /*if ($response-> != 'OK') {
         echo 'yo';
         return null;
       }
       */
        return $coordinates;
    }
}
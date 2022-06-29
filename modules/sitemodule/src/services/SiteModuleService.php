<?php
/**
 * SiteModule module for Craft CMS 3.x
 *
 * An example module for Craft CMS 3 that lets you enhance your websites with a custom site module
 *
 * @link      https://byhoffman.com/
 * @copyright Copyright (c) 2018 byhoffman
 */

namespace modules\sitemodule\services;

use modules\sitemodule\SiteModule;

use Craft;
use craft\base\Component;

/**
 * SiteModuleService Service
 *
 * All of your module’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other modules can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    byhoffman
 * @package   SiteModule
 * @since     1.0.0
 */
class SiteModuleService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin/module file, call it like this:
     *
     *     SiteModule::$instance->siteModuleService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }

    public function getClinicsByDistance($origin, $showAll) {
      $origin = 'j4t1k8';

      $criteria = Entry::find()
        ->section('clinics');

      $destinations = '';
      $clinicArray = [];

      $i = 0;
      $elementCount = count($criteria);
      foreach($criteria as $clinic) {
        $destinations =  $destinations . str_replace(' ','',$clinic->clinicPostalCode) . ($i == $elementCount - 1 ? '' : '|');
        $clinicArray[] = ['clinic' => $clinic, 'distance' => 0];
        $i++;
      }

      $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=".$destinations."&language=fr-CAregion=ca&key=AIzaSyDNUD6wYgRb5h_GjCu9eS2DGGAuYRkMPEY";
      $data   = @file_get_contents($url);
      $result = json_decode($data, true);

      $distanceI = 0;
      foreach($result['rows'][0]['elements'] as $clinic) {
        $clinicArray[$distanceI]['distance'] = $clinic['distance']['value'];
        $distanceI++;
      }

      usort($clinicArray, function($a, $b) {
        return $a['distance'] <=> $b['distance'];
      });

      var_dump($clinicArray);
      return true;
    }
}

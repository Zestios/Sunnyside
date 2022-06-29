<?php
/**
 * SiteModule module for Craft CMS 3.x
 *
 * An example module for Craft CMS 3 that lets you enhance your websites with a custom site module
 *
 * @link      https://byhoffman.com/
 * @copyright Copyright (c) 2018 byhoffman
 */

namespace modules\sitemodule\console\controllers;

use modules\sitemodule\SiteModule;

use Craft;
use craft\elements\Entry;
use craft\elements\Category;

use yii\console\Controller;
use yii\helpers\Console;

use \GuzzleHttp;
use \GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * Default Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is module-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft site-module/default
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft site-module/default/do-something
 *
 * @author    byhoffman
 * @package   SiteModule
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle site-module/default console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console DefaultController actionIndex() method\n";

        return $result;
    }

    /**
     * Handle site-module/default/sync-barnet-data console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionSyncBarnetData()
    {
        $success = false;

        echo "Sync'in Barnet API Data into our Craft Database\n";

        try {

            // Get the API url
            $client = new GuzzleHttp\Client();
            $response = $client->get('https://cannabis.barnetportal.com/ht/api/objects/products/details-by-stores', [
                'auth' => [
                    'ba8617b9659948ee',
                    'bf363ad8ae075cfa'
                ]
            ]);

            // Response
            var_dump($response->getStatusCode());
            var_dump($response->getReasonPhrase());
            // print_r(count(json_decode($response->getBody()->getContents())));die(); // 8
            // print_r(json_decode($response->getBody()->getContents())[2]->code);die(); // AZ
            print_r(json_decode($response->getBody()->getContents())[2]->code); // AZ

        } catch
        (\Exception $e) {
            // Catch and display errors if it fails
            var_dump('Error: Couldn’t get Barnet Data.');
            var_dump($e->getResponse());
        }

        // Try and find the Entry to see if it exists
        $existingEntry = Entry::find()
                        ->teamJobTitle('I can’t believe I literally just called this “Hello World!”.')
                        ->one();

        // If it does not, we create it
        if (!$existingEntry) {
            $entry = new Entry();
            $entry->sectionId = 13;
            $entry->typeId = 13;
            $entry->authorId = 1;
            $entry->enabled = true;
        } else {
            $entry = $existingEntry;
        }

        // Updating the entry informations
        $entry->title = "Hello World!";
        $entry->setFieldValues([
            'teamJobTitle' => "I can’t believe I literally just called this “Hello World!”.",
        ]);

        // Save
        $success = Craft::$app->elements->saveElement($entry);
        if (!$success) {
            Craft::error('Error: Couldn’t save the entry "'.$entry->title.'"', __METHOD__);
        }

        // Try and find the Entry to see if it exists
        $existingCategory = Category::find()
                        ->title('Hello World!')
                        ->one();

        // If it does not, we create it
        if (!$existingCategory) {
            $category = new Category();
            $category->groupId = 3;
            $category->enabled = true;
        } else {
            $category = $existingCategory;
        }

        // Updating the category informations
        $category->title = "Hello World!";

        // Save
        $success = Craft::$app->elements->saveElement($category);
        if (!$success) {
            Craft::error('Error: Couldn’t save the category "'.$category->title.'"', __METHOD__);
        }

        // Return Success
        return $success;
    }
}

<?php
/**
 * SiteModule module for Craft CMS 3.x
 *
 * An example module for Craft CMS 3 that lets you enhance your websites with a custom site module
 *
 * @link      https://byhoffman.com/
 * @copyright Copyright (c) 2018 byhoffman
 */

namespace modules\sitemodule\controllers;

use modules\sitemodule\SiteModule;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\Json;

// use craft\helpers\JsonHelper;
use craft\helpers\UrlHelper;
use phpDocumentor\Reflection\Types\Array_;
use SoapClient;
use SoapFault;
use SoapHeader;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your module’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    byhoffman
 * @package   SiteModule
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index',
        'get-soap-services', 'clinic-feed',
        'get-distance', 'test-soap-client',
        'get-clinics-availabilities', 'send-appointment'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/site-module/default
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DefaultController actionIndex() method';

        return $result;
    }

    public function actionTestSoapClient()
    {
        ini_set("default_socket_timeout", 200);

        //New Secured Soap
        $soapClient = new \SoapClient("http://websitewebserviceoauth2.azurewebsites.net/Service.svc?wsdl");

        $dataUser = [
            'username' => 'oauthEquilibre',
            'pwd' => 'EquilibreOauth2020!!',
            'isTokenRefresh' => false
        ];

        $accessToken = $soapClient->getAccessToken($dataUser);
        $header = new \SoapHeader("http://tempuri.org", "Access-Token", $accessToken->GetAccessTokenResult, false);
        $soapClient->__setSoapHeaders($header);

        $postData = Craft::$app->request->getBodyParams();

        $createTime = date('c', strtotime('now'));

        try {
            //L'heure du rendez-vous. Pas besoin de formatter ce que Acumatica envoi (Disponibility->Date)
            $appointmentDate = date('c', strtotime('2021-01-11T19:30:00-05:00'));

            //L'heure de fin de rdv. Ajoute Disponibility->Duration à Disponibility->Date
            $appointmentEndDate = date('c', strtotime('2021-01-11T21:00:00-05:00'));

            //L'heure de la soumission. Pas obliger de m'envoyer ça, ça peut être le 'now' à l'appel (CreatedDateTime)
            $createTime = date('c', strtotime('now'));

            $appointmentData = [
                'newEvent' => [
                    'OwnerID' => '6DF19ED6-8161-4BCA-9012-D29231A5272E', // Disponibility->Owner
                    'StartDate' => str_replace('-05:00', '', $appointmentDate),
                    'EndDate' => str_replace('-05:00', '', $appointmentEndDate),
                    'Location' => '28', //SubID
                    'UsrBranchCD' => '113', //BranchID en string
                    'Priority' => 1,
                    'PercentCompletion' => 0,
                    'UIStatus' => 'OP',
                    'ClassId' => 2,
                    'AllDay' => 100,
                    'CreatedByScreenID' => "EQUIL",
                    'CreatedDateTime' => str_replace('-05:00', '', $createTime),
                    'LastModifiedByScreenID' => "EQUIL",
                    'LastModifiedDateTime' => str_replace('-05:00', '', $createTime),
                    'CompanyID' => 5,
                    'UsrNote' => 'Test hoffman', //Note du user s'il y en a
                    'Subject' => 'Eval Test Hoffman', //Disponibility->Subject + Sujet du rendez-vous?
                    //Pour le body : firstName, lastName, birthday, codePostal, ville,
                    // confirmationType (Email, telephone, autre?), phone, phone2, email, parent:null, parentType:null
                    'Body' => 'nom :Southiphonh|prénom:Willy|Date de naissance: 1900-01-01 |Code postal: | ville:Montréal|type Confirmation: email | téléphone: |Autre:  |courriel :   willy@agencehoffman.com |parent:Willy Southiphonh|parent type:'

                ],
                'firstName' => 'Willy',
                'lastName' => 'Southiphonh',
                'Adresse' => '2727 rue Saint-Patrick',
                'Ville' => 'Montréal',
                'pain' => 'pied',
                'Time' => '14h00',
                'Orthesiste' => 'Diane Burelle' //Disponibility->OwnerFullName
            ];


            $availableClinics = $soapClient->EmpSchedule_Create($appointmentData);

            return json_encode($availableClinics->EmpSchedule_CreateResult);
        } catch (\SoapFault $fault) {
            var_dump($fault);
        }
    }

    public function actionSendAppointment()
    {
        ini_set("default_socket_timeout", 200);

        //New Secured Soap
        $soapClient = new \SoapClient("https://equilibrewebserviceprod.azurewebsites.net/Service.svc?wsdl");

        $dataUser = [
            'username' => 'oauthEquilibre',
            'pwd' => 'EquilibreOauth2020!!',
            'isTokenRefresh' => false
        ];

        $accessToken = $soapClient->getAccessToken($dataUser);
        $header = new \SoapHeader("http://tempuri.org", "Access-Token", $accessToken->GetAccessTokenResult, false);
        $soapClient->__setSoapHeaders($header);

        $postData = Craft::$app->request->getBodyParams();

        try {
            //L'heure de la soumission. Pas obliger de m'envoyer ça, ça peut être le 'now' à l'appel (CreatedDateTime)
            $createTime = date('c', strtotime('now'));
            $formatTime = date('H\hi', strtotime($postData['StartDate'])); //Format ISO-8601

            $body = '';
            $body .= 'Nom : ' . $postData['LastName'] ?? '';
            $body .= ' | Prénom: ' . $postData['FirstName'] ?? '';
            $body .= ' | Date de naissance: ' . $postData['Birthday'] ?? '';
            $body .= ' | Code Postal: ' . $postData['PostalCode'] ?? '';
            $body .= ' | Ville: ' . $postData['City'] ?? '';
            $body .= ' | type Confirmation: ' . $postData['ConfirmationType'] ?? '';
            $body .= ' | Téléphone: ' . $postData['Phone'] ?? '';
            $body .= ' | Autre:  ' . $postData['Phone2'] ?? '';
            $body .= ' | Courriel : ' . $postData['Email'] ?? '';
            $body .= ' | parent: ' . $postData['ParentName'] ?? '';
            $body .= ' | parent type:' . $postData['ParentType'] ?? '';

            $appointmentData = [
                'newEvent' => [
                    'OwnerID' => $postData['OwnerID'],
                    'StartDate' => $postData['StartDate'],
                    'EndDate' => $postData['EndDate'],
                    'Location' => intval($postData['Location']),
                    'UsrBranchCD' => $postData['UsrBranchCD'],
                    'Priority' => 1,
                    'PercentCompletion' => 0,
                    'UIStatus' => 'OP',
                    'ClassId' => 2,
                    'AllDay' => 0,
                    'CreatedByScreenID' => "EQUIL",
                    'CreatedDateTime' => str_replace('-05:00', '', $createTime),
                    'LastModifiedByScreenID' => "EQUIL",
                    'LastModifiedDateTime' => str_replace('-05:00', '', $createTime),
                    'CompanyID' => 5,
                    'UsrNote' => $postData['UsrNote'] ?? '',
                    'Subject' => $postData['Subject'] ?? '',
                    'Body' => $body

                ],
                'firstName' => $postData['FirstName'],
                'lastName' => $postData['LastName'],
                'Adresse' => $postData['Adresse'] ?? '',
                'Ville' => $postData['City'] ?? '',
                'pain' => $postData['Pain'] ?? '',
                'Time' => $formatTime,
                'Orthesiste' => $postData['Orthesiste'] ?? ''
            ];

            $availableClinics = $soapClient->EmpSchedule_Create($appointmentData);
            return json_encode($availableClinics->EmpSchedule_CreateResult);
        } catch (\SoapFault $fault) {
            return json_encode($fault);
        }
    }

    public function actionGetClinicsAvailabilities()
    {
        ini_set("default_socket_timeout", 200);
        $soapClient = new \SoapClient("https://equilibrewebserviceprod.azurewebsites.net/Service.svc?wsdl");
        $partieCorps = Craft::$app->request->getParam('patieCorps');
        $clinicLists = Craft::$app->request->getParam('cliniques');
        $clinicLists = array_map('intval', explode(',', $clinicLists));

        $dataUser = [
            'username' => 'oauthEquilibre',
            'pwd' => 'EquilibreOauth2020!!',
            'isTokenRefresh' => false
        ];

        $accessToken = $soapClient->getAccessToken($dataUser);
        $header = new \SoapHeader("http://tempuri.org", "Access-Token", $accessToken->GetAccessTokenResult, false);
        $soapClient->__setSoapHeaders($header);

        try {
            $dataBranch = [
                'patieCorps' => $partieCorps,
                'cliniques' => $clinicLists
            ];

            $availableClinics = $soapClient->GetBranchAvailabilities($dataBranch);

            return json_encode($availableClinics->GetBranchAvailabilitiesResult->AvailabilityByBranches);

        } catch (\SoapFault $fault) {
            $errorResponse = [
                'ResultType' => "Failed"
            ];

            return json_encode($errorResponse);
        }
    }

    /**
     * Returns all the available SOAP services
     * e.g.: actions/site-module/default/get-soap-services
     *
     * @return mixed
     */
    public function actionGetSoapServices()
    {
        ini_set("default_socket_timeout", 200);

        //New Secured Soap
        $soapClient = new \SoapClient("https://equilibrewebserviceprod.azurewebsites.net/Service.svc?wsdl");

        $dataUser = [
            'username' => 'oauthEquilibre',
            'pwd' => 'EquilibreOauth2020!!',
            'isTokenRefresh' => false
        ];

        $accessToken = $soapClient->getAccessToken($dataUser);
        $header = new \SoapHeader("http://tempuri.org", "Access-Token", $accessToken->GetAccessTokenResult, false);
        $soapClient->__setSoapHeaders($header);

        echo '<pre>';
        var_dump($soapClient->__getTypes());

        die();
        return true;
    }
}

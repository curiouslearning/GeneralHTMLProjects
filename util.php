<?php
if(!isset($_SESSION))
{
    session_start();
}
if(file_exists('includes/checklogin.php'))
    include_once('includes/checklogin.php');
elseif(file_exists('checklogin.php'))
    include_once('checklogin.php');
require_once("transporter.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/../db.config");
//$util = new Util();
//print_r($util->getDeploymentsTabletsManifest());
class Util
{
    public function getActiveTabletCount()
    {
        $trans = new Transporter();
        $userId = $_SESSION['userId'];
        $query = "select retrieve_active_tablets('$userId');";
        $result = $trans->sendQuery($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            die($message);
        }
        else
        {
            $db_field = mysql_fetch_array($result);
            if(!empty($db_field[0]))
            {
                return $db_field[0];
            }
        }
    }
    public function getRegisteredUsersCount()
    {
        $trans = new Transporter();
        $query = "select retrieve_registered_users_count();";
        $result = $trans->sendQuery($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            die($message);
        }
        else
        {
            $db_field = mysql_fetch_array($result);
            if(!empty($db_field[0]))
            {
                return $db_field['retrieve_registered_users_count()'];
            }
        }
    }
    public function getDeploymentCount()
    {
        $trans = new Transporter();
        $query = "select retrieve_deployment_count();";
        $result = $trans->sendQuery($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            die($message);
        }
        else
        {
            $db_field = mysql_fetch_array($result);
            if(!empty($db_field[0]))
            {
                return $db_field['retrieve_deployment_count()'];
            }
        }
    }
    public function createClassAndAddTablets($managerId, $schoolId, $tabletGroup, $groupName)
    {
        $trans = new Transporter();
        $query = "SELECT create_class($managerId, $schoolId, '$groupName');";
        $result = $trans->sendQuery($query);
        $classId = mysql_fetch_array($result)[0];
        //Insert all tablets into the new class
        foreach($tabletGroup as $tablet)
        {
            $query = "SELECT add_tablet_to_class( $tablet, $classId);";
            $trans->sendQuery($query);
        }
        return true;
    }
    public function getDataProcessingDate()
    {
        date_default_timezone_set("America/New_York");
        if(TEST_MODE)
           return date("F j, Y");
        //!!Remove after view implementation!!//
        return date("F j, Y");
        $trans = new Transporter();
        $query = "select retrieve_last_data_processing_date();";
        $result = $trans->sendQuery($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            die($message);
        }
        else
        {
            $db_field = mysql_fetch_array($result);
            if(!empty($db_field[0]))
            {
                return $db_field['retrieve_last_data_processing_date()'];
            }
        }
    }
    public function changePassword($currentPassword, $newPassword1, $newPassword2)
    {
        $trans = new Transporter();
        if($newPassword1 !== $newPassword2)
            return "Your new password's do not match!";
        if(!$trans->checkLogin($_SESSION['username'], $currentPassword))
            return "Your username and password do not match";
        if($trans->updatePassword($_SESSION['username'], $currentPassword, $newPassword1))
            return true;
        else
            return "Error Updating Password";
    }
    public function createNewUser($username, $password, $accessLevel, $fullName, $location, $deploymentId, $country, $schoolManager)
    {
        $trans = new Transporter();
        return $trans->createUser($username, $password, $accessLevel, $fullName, $location, $deploymentId, $country, $schoolManager);
    }
    public function createNewDeploymentPartner($location, $tabletCount, $tabletType, $deploymentPartner, $email, $country, $startDate)
    {
        $trans = new Transporter();
        $query = "SELECT add_deployment_partner('$location','$tabletCount','$tabletType','$deploymentPartner','$email', '$country', '$startDate', '1');";
        $result = $trans->sendQuery($query);
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            die($message);
        }
        else
        {
            $db_field = mysql_fetch_array($result);
                return $db_field[0];
        }
    }
    public function getDeploymentInformation($deploymentNumber = 0)
    {
        $trans = new Transporter();
        $query = "select * from deployment_information_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getTabletInformation()
    {
        $trans = new Transporter();
        $query = "select * from broad_tablet_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getTabletInfoFromSerialId($serialId)
    {
        $trans = new Transporter();
        $query = "SELECT
	ti.serial_id as 'Serial ID'
	, ti.label as 'Label'
	, ti.tablet_type as 'Tablet Type'
	, ti.using_raspberry_pi as 'Using Pi'
	, uc.full_name as 'Deployment Contact'
	, di.location as 'Location'
	, ti.last_ping_time as 'Last Ping'
	, di.is_active as 'Is Active'
FROM
	user_credentials as uc
	, tablet_information ti
	, deployment_information di
	, user_deployment as ud
WHERE
	di.id = ud.deployment_key
    AND uc.id = ud.user_key
    AND ti.user_credential_key = ud.user_key
    AND ti.serial_id = '$serialId';";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getDeploymentUsersWithDeployments()
    {
        $trans = new Transporter();
        $query = "select * from deployment_users_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getDeploymentPartners()
    {
        $trans = new Transporter();
        $query = "select * from deployment_partners_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getAllUsers()
    {
        $trans = new Transporter();
        $query = "select * from all_users_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getTicketTypes()
    {
        $trans = new Transporter();
        $query = "select id, ticket_type, name, description from problem_ticket_types;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function createProblemTicket($userId, $priority, $location, $deployment, $description, $ticketType)
    {
        $trans = new Transporter();
        date_default_timezone_set("America/New_York");
        $now= date('Y-m-d H:i:s');
        $query = "SELECT create_ticket('$userId','$priority','$location','$deployment','$description','$ticketType','$now');";
   //     echo $query;
        $trans->sendQuery($query);
    }
    public function listUsersByCreator($creator)
    {
        $trans = new Transporter();
        if($creator <= 1)  //If admin, return all users
        {
            $query = "select uc.id, uc.full_name, uc.school_id,  ds.name
                from user_credentials as uc, deployment_school as ds
                where uc.created_by = $creator
                GROUP BY uc.id;";
        }
        else
        {
            $query = "select uc.id, uc.full_name, uc.school_id,  ds.name
                from user_credentials as uc, deployment_school as ds
                where uc.created_by = $creator
                GROUP BY uc.id;";
        }
        $result = $trans->sendQuery($query);
        return $result;
    }
    public function getDistrictManagers()
    {
        $trans = new Transporter();
        $query = "select * from deployment_district_managers_view;";
        $result = $trans->sendQuery($query);
        if(!mysql_fetch_assoc($result))
        {
            echo("No Mangers Created at this time");
            return false;
        }
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function listTabletsBySchool($schoolId)
    {
        $trans = new Transporter();
        $query = "SELECT ti.id, ti.label FROM
                    deployment_information AS di
                    , tablet_information AS ti
                    , deployment_school AS ds
                    WHERE ti.deployment_information_key = di.id
                    AND ds.id = $schoolId
                    AND ds.deployment_id = di.id
                    GROUP BY ti.id
                    ORDER BY ti.label";
        $result = $trans->sendQuery($query);
        return $result;
    }
    public function getSchoolManagers()
    {
        $trans = new Transporter();
        $query = "select * from deployment_school_managers_view;";
        $result = $trans->sendQuery($query);
        if(!mysql_fetch_assoc($result))
        {
            echo("No Mangers Created at this time");
            return false;
        }
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function getTeachers()
    {
        $trans = new Transporter();
        $query = "select * from deployment_teachers_view;";
        $result = $trans->sendQuery($query);
        if(!mysql_fetch_assoc($result))
        {
            echo("No Mangers Created at this time");
            return false;
        }
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    public function addUserToSchool($deploymentManagerId, $creatorUserId, $schoolId)
    {
        $trans = new Transporter();
        date_default_timezone_set("America/New_York");
        $now= date('Y-m-d H:i:s');
        $query = "UPDATE user_credentials SET school_id = $schoolId,  modified_date = '$now' where id = $deploymentManagerId;";
        $result = $trans->sendQuery($query);
        if(!$result)
            return false;
        return true;
    }
    public function generatePassword()
    {
        //Auto generate password
        $password = substr(md5(rand()), 0, 7);
        return $password;
    }
    public function saveUser($username, $email, $fullName, $accessLevel, $password, $createdBy, $location, $country)
    {
        $trans = new Transporter();
        $password = crypt($password, SALT);
        $query = "SELECT add_user('$email', '$fullName', '$accessLevel', '$password', '$createdBy', '$username', '$location', '$country');";
        $result = $trans->sendQuery($query);
        $feedback = mysql_fetch_array($result);
        return $feedback[0];
    }
    public function saveUserMetaInfo($userId, $firstName, $lastName, $skypeName, $phoneNumber, $country, $state, $city, $email)
    {
        $trans = new Transporter();
        $query = "SELECT create_user_information('$userId', '$firstName', '$lastName', '$skypeName', '$phoneNumber', '$country', '$state', '$city', '$email');";
        return $trans->sendQuery($query);
    }
    public function createDeployment($username,  $password, $accessLevel, $fullName, $location, $country, $email,
                                     $tabletCount, $tabletType, $deploymentPartner, $isSchoolManager,
                                     $deploymentName, $city, $state, $streetAddress, $date)
    {
        $trans = new Transporter();
        $query = "SELECT create_deployment('$username', '$password','$accessLevel','$fullName','$location','$country',
        '$email','$tabletCount','$tabletType','$deploymentPartner','$isSchoolManager','$deploymentName','$city',
        '$state','$streetAddress', '$date');";
        $result = $trans->sendQuery($query);
        return $result;
    }
    public function saveDistrictManager($userId, $createdBy, $country, $districtName, $districtCity, $districtState)
    {
        $trans = new Transporter();
        // Add the admin information to the DB
        $query = "select create_district_manager('$userId','$districtName','$createdBy','$country','$districtCity','$districtState');";
        $result = $trans->sendQuery($query);
        $districtId = mysql_fetch_array($result);
        return $districtId;
    }
    public function addSchoolToDeployment($schoolId, $districtId)
    {
        $trans = new Transporter();
        $query = "SELECT add_school_to_district('$schoolId', '$districtId');";
        $result = $trans->sendQuery($query);
        return mysql_fetch_array($result);
    }
    public function getActiveAndNonActiveTabletsByCountry()
    {
        $trans = new Transporter();
        $query = "select * from tablets_by_country_view;";
        $result = $trans->sendQuery($query);
        $rows = array();
        while($r = mysql_fetch_assoc($result)) {
            $rows[] = $r;
        }
        return $rows;
    }
    private function executePdoQuery($query)
    {
        $dbh = $this->pdoDbConnect();
        if($dbh == null)
            die("Unable to establish database connection");
        $statement = $dbh->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    }
//
//    public function getAllApps()
//    {
//        $select = "select * from app_information;";
//        return $this->executePdoQuery($select);
//    }
    public function getDeploymentsManifest()
    {
        $transporter = new Transporter();
        $query = "SELECT dm.*, mpi.management_app_version
            FROM deployments_manifest dm, management_ping_information mpi
            WHERE dm.tablet_key = mpi.tablet_id
                AND is_current = 1;";
        $result = $transporter->sendQuery($query);
        $firstRow = mysql_fetch_array($result);
        $currentDeploymentId = $firstRow['deployment_key'];
        $deployments = array();
        $deployment = array();
        $currentDeployment = $deployment['deployment'] =
            $firstRow['location'] ." - " . $firstRow['country_code'];
        $deployment['deploymentId'] = $firstRow['deployment_key'];
        $data = array();
        $data[] = array('label' => $firstRow['tablet_label']
                        , 'desiredManifest' => $firstRow['desired_version']
                        , 'currentManifest' => $firstRow['current_version']
                        , 'managementAppVersion' => $firstRow['management_app_version']
                );
        while($row = mysql_fetch_array($result))
        {
            if($row['deployment_key'] != $currentDeploymentId)
            {
                $deployment['data'] = $data;
                $deployments[] = $deployment;
                $deployment = array();
                $deployment['deployment'] =
                    $row['location'] ." - " . $row['country_code'];
                $currentDeploymentId = $row['deployment_key'];
                $deployment['deploymentId'] = $currentDeploymentId;
                $currentDeployment = $row['location'] ." - " . $row['country_code'] ;
                $data = array();
            }
            $data[] = array('label' => $row['tablet_label']
            , 'desiredManifest' => $row['desired_version']
            , 'currentManifest' => $row['current_version']
            , 'managementAppVersion' => $row['management_app_version']
            );
        }
        $deployment['deployment'] = $currentDeployment;
        $deployment['deploymentId'] = $currentDeploymentId;
        $deployment['data'] = $data;
        $deployments[] = $deployment;
        //print_r(json_encode(array('deployments' => $deployments)));
        return(json_encode(array('deployments' => $deployments)));
    }
    public function getDeploymentsTabletsManifest()
    {
//        $apcKey = "deploymentsTabletsManifest";
//        $isFound = false;
//        $result = apc_fetch($apcKey, $isFound);
//        if($isFound)
//            return $result;
        //Get the DB handle
        $dbh = $this->pdoDbConnect();
        if($dbh == null)
            die("Unable to establish database connection");
        $select = "CALL get_all_manifest_information;";
        $statement = $dbh->prepare($select);
        $statement->execute();
        $select = "SELECT deployment_id
                    , manifest_version
                    , app_id
                    , video_id
                    , location
                    , country_code
                    , content_type
                    , is_visible
                  FROM temp_manifest_information";
        $statement = $dbh->prepare($select);
        $statement->execute();
        $result = $statement->fetchAll();
//        apc_add($apcKey, $result, 600);
        $dbh = null;
        return $result;
    }
    private function pdoDbConnect()
    {
        try
        {
            $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE,
                DB_USERNAME, DB_PASSWORD);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            return null;
        }
    }
    private function getManifestFromDatabase($id)
    {
        $transporter = new Transporter();
        $query = "SELECT manifest_version from manifest_information
            WHERE WHERE deployment_id = $id
            ORDER BY date_created DESC
            LIMIT 1";
        return mysql_fetch_array($transporter->sendQuery($query))[0];
    }
    private function getInstalledApps($manifest)
    {
        $transporter = new Transporter();
        $query = "
        SELECT
            mita.app_information_id
            , ai.title
            , ai.category
            , ai.description
            , mita.is_visible
        FROM
            manifest_information_to_app as mita
            , app_information as ai
            , manifest_information as mi
        WHERE
            mita.manifest_information_id = mi.id
            AND mi.manifest_version = '$manifest'
            AND mita.app_information_id = ai.id;";
        $result = $transporter->sendQuery($query);
        $idSet = array();
        while($row = mysql_fetch_assoc($result)) {
            $idSet[] = array(
            "id" => $row['app_information_id']
            , "isInstalled" => 1
            , "appTitle" => $row['title']
            , "category" => $row['category']
            , "description" => $row['description']
            , "is_visible" => $row['is_visible']
            );
        }
        return $idSet;
    }
    public function getAllApps()
    {
        $ids = array();
        $transporter = new Transporter();
        $query = "SELECT id, title, category, description, content_type
          FROM app_information
          WHERE is_in_bucket = 1;";
        $result = $transporter->sendQuery($query);
        $idSet = array();
        while($row = mysql_fetch_assoc($result))
        {
            $ids[] = $row['id'];
            $idSet[] = array(
                "id" => $row['id']
                , "isInstalled" => 0
                , "title" => $row['title']
                , "category" => $row['category']
                , "description" => $row['description']
                , "content_type" => $row['content_type']
            );
        }
        return array($idSet, $ids);
    }
    public function getAllVideos()
    {
        $ids = array();
        $transporter = new Transporter();
        $query = "SELECT id, title, category, description, content_type
          FROM video_information
          WHERE is_in_bucket = 1;";
        $result = $transporter->sendQuery($query);
        $idSet = array();
        while($row = mysql_fetch_assoc($result))
        {
            $ids[] = $row['id'];
            $idSet[] = array(
            "id" => $row['id']
            , "isInstalled" => 0
            , "title" => $row['title']
            , "category" => $row['category']
            , "description" => $row['description']
            , "content_type" => $row['content_type']
            );
        }
        return array($idSet, $ids);
    }
//    public function saveNewDeploymentManifest($deploymentID, $appsToBeUsed)
//    {
//        $manifestBuilder = new ManifestBuilder();
//        $manifestBuilder->buildNewManifest($deploymentID, $appsToBeUsed);
//    }
    public function getCountryCode($code)
    {
        if(strlen($code) != 2)
            return "";
        $countries = array
        (
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );
        return $countries[$code];
    }
}

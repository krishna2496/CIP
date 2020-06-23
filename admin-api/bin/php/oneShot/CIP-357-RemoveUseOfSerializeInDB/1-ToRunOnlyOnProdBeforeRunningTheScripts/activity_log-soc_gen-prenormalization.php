<?php
require_once('bootstrap/app.php');

$db = app()->make('db');
$db->purge('tenant');
// Create connection to tenant
\Illuminate\Support\Facades\Config::set('database.connections.tenant', array(
    'driver' => 'mysql',
    'host' => env('DB_HOST'),
    'database' => 'ci_tenant_7',
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
));
// Create connection for the tenant database
$pdo = $db->connection('tenant')->getPdo();
// Set default database
\Illuminate\Support\Facades\Config::set('database.default', 'tenant');

$rows = $pdo->query('SELECT activity_log_id, object_value FROM activity_log')->fetchAll();

foreach ($rows as $row) {
    if ($row['object_value'] === null) {
        continue;
    }


    $id = $row['activity_log_id'];
    try {
        $value = unserialize($row['object_value']);
        $pdo->exec('SET NAMES utf8mb4');
        $pdo->exec('SET CHARACTER SET utf8mb4');

        $pdo->prepare('
        UPDATE activity_log
        SET object_value = :object_value
        WHERE activity_log_id = :id
    ')
            ->execute([
                'object_value' => serialize($value),
                'id' => $id
            ]);

    } catch (\Exception $exception) {
        // Data is already well encoded, do nothing
        continue;
    }
}

//$rowsToUpdate = [
//    2 => 'a:2:{s:10:"theme_name";s:19:"Financial education";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:19:"Financial education";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:21:"Education financière";}}}',
//    4 => 'a:2:{s:10:"theme_name";s:14:"Solidarity day";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:14:"Solidarity day";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:18:"Journée solidaire";}}}',
//    5 => 'a:2:{s:10:"theme_name";s:12:"Pro bono day";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:12:"Pro bono day";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:17:"Journée pro bono";}}}',
//    6 => 'a:2:{s:10:"theme_name";s:28:"Punctual skill-based mission";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:28:"Punctual skill-based mission";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:42:"Mission de mécénat de compétences Flash";}}}',
//    10 => 'a:3:{s:10:"skill_name";s:25:"Ability to work as a team";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:25:"Ability to work as a team";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:34:"Capacité à travailler en équipe";}}}',
//    11 => 'a:3:{s:10:"skill_name";s:27:"Computer and digital skills";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:27:"Computer and digital skills";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:41:"Compétences informatiques et numériques";}}}',
//    12 => 'a:3:{s:10:"skill_name";s:30:"Legal or administrative skills";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:30:"Legal or administrative skills";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:42:"Compétences juridiques ou administratives";}}}',
//    13 => 'a:3:{s:10:"skill_name";s:49:"Marketing, communication or social network skills";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:49:"Marketing, communication or social network skills";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:58:"Compétences en marketing, communication, réseaux sociaux";}}}',
//    15 => 'a:3:{s:10:"skill_name";s:31:"Accounting and financial skills";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:31:"Accounting and financial skills";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:41:"Compétences budgétaires et financières";}}}',
//    16 => 'a:3:{s:10:"skill_name";s:30:"Skills in business development";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:30:"Skills in business development";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:41:"Compétences en développement commercial";}}}',
//    17 => 'a:3:{s:10:"skill_name";s:28:"Listening skills and empathy";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:28:"Listening skills and empathy";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:31:"Capacité d\'écoute et empathie";}}}',
//    20 => 'a:3:{s:10:"skill_name";s:21:"Sharing of experience";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:21:"Sharing of experience";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:21:"Partage d\'expérience";}}}',
//    21 => 'a:3:{s:10:"skill_name";s:28:"Professional network sharing";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:28:"Professional network sharing";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:32:"Partage de réseau professionnel";}}}',
//    22 => 'a:3:{s:10:"skill_name";s:33:"Personal development and coaching";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:33:"Personal development and coaching";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:36:"Développement personnel et coaching";}}}',
//    23 => 'a:3:{s:10:"skill_name";s:19:"Conflict resolution";s:12:"parent_skill";i:0;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:19:"Conflict resolution";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:25:"Résolution de problèmes";}}}',
//    34 => 'a:2:{s:4:"type";s:8:"Work day";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:8:"Work day";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:19:"Journée de travail";}}}',
//    36 => 'a:2:{s:4:"type";s:7:"Holiday";s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:7:"Holiday";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:6:"Congé";}}}',
//    39 => 'a:4:{s:4:"name";s:23:"external_approver_email";s:4:"type";s:4:"text";s:12:"is_mandatory";b:1;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:4:"name";s:20:"Line manager - Email";}i:1;a:2:{s:4:"lang";s:2:"fr";s:4:"name";s:32:"Supérieur hiérarchique - Email";}}}',
//    40 => 'a:4:{s:4:"name";s:23:"external_approver_title";s:4:"type";s:4:"text";s:12:"is_mandatory";b:1;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:4:"name";s:20:"Line manager - Title";}i:1;a:2:{s:4:"lang";s:2:"fr";s:4:"name";s:32:"Supérieur hiérarchique - Titre";}}}',
//    41 => 'a:4:{s:4:"name";s:27:"external_approver_firstname";s:4:"type";s:4:"text";s:12:"is_mandatory";b:1;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:4:"name";s:25:"Line manager - First name";}i:1;a:2:{s:4:"lang";s:2:"fr";s:4:"name";s:34:"Supérieur hiérarchique - Prénom";}}}',
//    42 => 'a:4:{s:4:"name";s:26:"external_approver_lastname";s:4:"type";s:4:"text";s:12:"is_mandatory";b:1;s:12:"translations";a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:4:"name";s:24:"Line manager - Last name";}i:1;a:2:{s:4:"lang";s:2:"fr";s:4:"name";s:30:"Supérieur hiérarchique - Nom";}}}',
//    43 => 'a:1:{s:12:"page_details";a:2:{s:4:"slug";s:18:"general-conditions";s:12:"translations";a:2:{i:0;a:3:{s:4:"lang";s:2:"en";s:5:"title";s:18:"General conditions";s:8:"sections";a:1:{i:0;a:2:{s:5:"title";s:30:"This website is published by :";s:11:"description";s:27:"<p>Hello this is a test</p>";}}}i:1;a:3:{s:4:"lang";s:2:"fr";s:5:"title";s:17:"Mentions légales";s:8:"sections";a:1:{i:0;a:2:{s:5:"title";s:25:"Ce site est publié par :";s:11:"description";s:28:"<p>Ceci est un <br> test</p>";}}}}}}',
//    46 => ,
//    47 => ,
//    56 => ,
//    57 => ,
//    61 => ,
//    63 => ,
//    64 => ,
//    65 =>
//];
//
//
//
//
//
//                    $pdo->prepare('
//                        UPDATE activity_log
//                        SET `object_value` = :object_value
//                        WHERE activity_log_id = :id
//                    ')
//                        ->execute([
//                            'object_value' => $jsonData,
//                            'id' => $tenantOption['activity_log_id']
//                        ]);
//
//            }
//        }
//    }
//}

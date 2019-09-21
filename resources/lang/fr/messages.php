<?php
return [
    
    /**
    * Success messages
    */
    'success' => [
        'MESSAGE_TENANT_CREATED' => 'Locataire créé avec succès',
        'MESSAGE_TENANT_UPDATED' => 'Détails du locataire mis à jour avec succès',
        'MESSAGE_TENANT_DELETED' => 'Locataire supprimé avec succès',
        'MESSAGE_TENANT_LISTING' => 'Locataires listés avec succès',
        'MESSAGE_NO_RECORD_FOUND' => 'Aucun enregistrement trouvé',
        'MESSAGE_TENANT_FOUND' => 'Locataire trouvé avec succès',
        'MESSAGE_TENANT_API_USER_LISTING' => 'Les utilisateurs de l\'API du locataire répertoriés avec succès',        
        'MESSAGE_API_USER_FOUND' => 'Utilisateur de l\'API trouvé avec succès',
        'MESSAGE_API_USER_CREATED_SUCCESSFULLY' => 'Utilisateur de l\'API créé avec succès',
        'MESSAGE_API_USER_DELETED' => 'Utilisateur de l\'API supprimé avec succès',
        'MESSAGE_API_USER_UPDATED_SUCCESSFULLY' => 'La clé secrète de l\'utilisateur de l\'API a été mise à jour avec succès',
        'MESSAGE_LANGUAGE_FOUND' => 'Langue trouvée avec succès',
        'MESSAGE_LANGUAGE_LISTING' => 'Langue listée avec succès',
        'MESSAGE_LANGUAGE_CREATED' => 'Langue créée avec succès',
        'MESSAGE_LANGUAGE_UPDATED' => 'Détails de la langue mis à jour avec succès',
        'MESSAGE_NEWS_DELETED' => 'Langue supprimée avec succès',
    ],
    
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        'ERROR_TENANT_REQUIRED_FIELDS_EMPTY' => 'Le nom ou le champ sponsorisé est vide',
        'ERROR_TENANT_ALREADY_EXIST' => 'Le nom du locataire est déjà pris, veuillez essayer avec un nom différent',
        'ERROR_TENANT_NOT_FOUND' => 'Non trouvé dans le système',
        'ERROR_DATABASE_OPERATIONAL' => 'Erreur opérationnelle de la base de données',
        'ERROR_NO_DATA_FOUND' => 'Aucune donnée disponible',
        'ERROR_INVALID_ARGUMENT' => 'argument invalide',
        'FAILED_TO_CREATE_FOLDER_ON_S3' => 'Erreur lors de la création du dossier sur le compartiment S3',
        'ERROR_API_USER_NOT_FOUND' => 'Utilisateur API introuvable',
        'ERROR_OCCURRED' => 'Une erreur est survenue',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'Fichier Boostrap SCSS introuvable lors de la compilation des fichiers SCSS',
        'ERROR_INVALID_JSON' => 'Format Json invalide',
        'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => 'Erreur lors du stockage de CSS compilé au niveau local',
        'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => 'Erreur lors du téléchargement du fichier CSS compilé vers S3',
        'ERROR_WHILE_COMPILING_SCSS_FILES' => 'Erreur lors de la compilation des fichiers SCSS',
        'ERROR_LANGUAGE_NOT_FOUND' => 'Langue non trouvée dans le système',
    ]
    
];

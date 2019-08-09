<?php

return [
    /**
    * API success messages
    */
    'success' => [
        'MESSAGE_USER_FOUND' => 'Utilisateur trouvé avec succès',
        'MESSAGE_NO_DATA_FOUND' => 'Aucune donnée disponible',
        'MESSAGE_USER_CREATED' => 'Utilisateur créé avec succès',
        'MESSAGE_USER_DELETED' => 'Utilisateur supprimé avec succès',
        'MESSAGE_FOOTER_PAGE_CREATED' => 'Page créée avec succès',
        'MESSAGE_FOOTER_PAGE_UPDATED' => 'Page mise à jour avec succès',
        'MESSAGE_FOOTER_PAGE_DELETED' => 'Page supprimée avec succès',
        'MESSAGE_FOOTER_PAGE_LISTING' => 'Pied de page listant avec succès.',
        'MESSAGE_USER_UPDATED' => 'Utilisateur mis à jour avec succès',
        'MESSAGE_NO_RECORD_FOUND' => 'Aucun enregistrement trouvé',
        'MESSAGE_USER_LISTING' => 'Liste d\'utilisateurs réussie',
        'MESSAGE_USER_SKILLS_CREATED' => 'Compétences utilisateur liées avec succès',
        'MESSAGE_USER_SKILLS_DELETED' => 'Compétences utilisateur non liées avec succès',
        'MESSAGE_SLIDER_ADD_SUCCESS' => 'Image du curseur ajoutée avec succès',
        'MESSAGE_USER_LOGGED_IN' => 'Vous êtes connecté avec succès',
        'MESSAGE_PASSWORD_RESET_LINK_SEND_SUCCESS' => 'Le lien Réinitialiser le mot de passe est envoyé à votre compte de messagerie, le lien expirera dans' . config('constants.FORGOT_PASSWORD_EXPIRY_TIME') . ' heures',
        'MESSAGE_PASSWORD_CHANGE_SUCCESS' => 'Votre mot de passe a été changé avec succès.',
        'MESSAGE_CUSTOM_FIELD_ADDED' => 'Champ personnalisé d\'utilisateur ajouté avec succès',
        'MESSAGE_CUSTOM_FIELD_UPDATED' => 'Le champ personnalisé de l\'utilisateur a été mis à jour avec succès',
        'MESSAGE_CUSTOM_FIELD_DELETED' => 'Le champ personnalisé de l\'utilisateur a été supprimé avec succès',
        'MESSAGE_APPLICATION_LISTING' => 'Liste des applications de mission avec succès',
        'MESSAGE_APPLICATION_UPDATED' => 'Application de mission mise à jour avec succès',
        'MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS' => 'Données de style personnalisées téléchargées avec succès',
        'MESSAGE_CUSTOM_STYLE_RESET_SUCCESS' => 'Style personnalisé réinitialisé avec succès',
        'MESSAGE_CUSTOM_FIELD_LISTING' => 'Liste personnalisée des champs utilisateur avec succès',
        'MESSAGE_MISSION_ADDED' => 'Mission créée avec succès',
        'MESSAGE_MISSION_UPDATED' => 'Mission mise à jour avec succès',
        'MESSAGE_MISSION_DELETED' => 'Mission supprimée avec succès',
        'MESSAGE_MISSION_LISTING' => 'Liste des missions réussie',
        'MESSAGE_SKILL_LISTING' => 'Liste de compétences avec succès',
        'MESSAGE_THEME_LISTING' => 'Liste de thèmes de mission réussie',
        'MESSAGE_CITY_LISTING' => 'Annonce de la ville avec succès',    
        'MESSAGE_MISSION_ADDED_TO_FAVOURITE' => 'Mission ajouté aux favoris',
        'MESSAGE_MISSION_DELETED_FROM_FAVOURITE' => 'Mission retirée du favori',
        'MESSAGE_COUNTRY_LISTING' => 'Liste de pays avec succès',
        'MESSAGE_MISSION_FOUND' => 'Mission trouvée avec succès',
        'MESSAGE_PAGE_FOUND' => 'Page trouvée avec succès',
        'MESSAGE_ASSETS_FILES_LISTING' => 'Liste des fichiers d\'actifs avec succès',
        'MESSAGE_TENANT_SETTING_UPDATE_SUCCESSFULLY' => 'Les paramètres ont été mis à jour avec succès',
        'MESSAGE_TENANT_SETTINGS_LISTING' => 'Liste des paramètres avec succès',
		'MESSAGE_THEME_CREATED' => 'Thème de mission créé avec succès',
        'MESSAGE_THEME_UPDATED' => 'Thème de la mission mis à jour avec succès',
        'MESSAGE_THEME_DELETED' => 'Thème de mission supprimé avec succès',
        'MESSAGE_THEME_FOUND' => 'Thème de la mission trouvé avec succès',
        'MESSAGE_SKILL_CREATED' => 'Compétence créée avec succès',
        'MESSAGE_SKILL_UPDATED' => 'Compétence mise à jour avec succès',
        'MESSAGE_SKILL_DELETED' => 'Compétence supprimée avec succès',
        'MESSAGE_SKILL_FOUND' => 'Compétence trouvée avec succès',
        'MESSAGE_THEME_FOUND' => 'Thème de la mission trouvé avec succès',
        'MESSAGE_TENANT_OPTION_CREATED' => 'Option de locataire créée avec succès',
        'MESSAGE_TENANT_OPTION_UPDATED' => 'Option de locataire mise à jour avec succès',
        'MESSAGE_TENANT_OPTIONS_LIST' => 'Liste des options locataires avec succès',
        'MESSAGE_MISSION_RATING_LISTING' => 'Obtenir une note de mission avec succès',
        'MESSAGE_TENANT_OPTION_FOUND' => 'Option locataire trouvée', 
        'MESSAGE_INVITED_FOR_MISSION' => 'Utilisateur invité à la mission avec succès',
        'MESSAGE_APPLICATION_CREATED' => 'Appliqué pour une mission avec succès',
        'MESSAGE_RATING_ADDED' => 'Évaluation de la mission ajoutée avec succès',
        'MESSAGE_RATING_UPDATED' => 'Evaluation de la mission mise à jour avec succès',
        'MESSAGE_MISSION_VOLUNTEERS_LISTING' => 'Obtenez des volontaires de mission avec succès',
        'MESSAGE_NO_MISSION_VOLUNTEERS_FOUND' => 'Aucun volontaire récent trouvé',
        'MESSAGE_MISSION_MEDIA_LISTING' => 'Les médias de la mission ont réussi',
        'MESSAGE_MISSION_COMMENT_LISTING' => 'Obtenir des commentaires de mission avec succès',
        'MESSAGE_ASSET_IMAGES_RESET_SUCCESS' => 'Les images d\'actif ont été réinitialisées avec succès',
        'MESSAGE_NO_RELATED_MISSION_FOUND' => 'Mission associée non trouvée',        
        'MESSAGE_NO_MISSION_MEDIA_FOUND' => 'Média de mission introuvable',
        'MESSAGE_NO_MISSION_COMMENT_FOUND' => 'Commentaires de mission non trouvés',  
        'MESSAGE_COMMENT_ADDED' => 'Commentaire de mission ajouté avec succès',
        'MESSAGE_POLICY_PAGE_LISTING' => 'Liste des pages de règles correctement.',        
        'MESSAGE_POLICY_PAGE_CREATED' => 'Page de stratégie créée avec succès',        
        'MESSAGE_POLICY_PAGE_DELETED' => 'Page supprimée avec succès',
        'MESSAGE_POLICY_PAGE_UPDATED' => 'La page de politique a été mise à jour avec succès',
    ],

        
    /**
    * API Error Codes and Message
    */
    'custom_error_message' => [
        // Custom error code for User Module - 100000 - 109999
        'ERROR_USER_NOT_FOUND' => 'Utilisateur non trouvé dans le système',
        'ERROR_SKILL_INVALID_DATA' => 'Données de compétence non valides',
        'ERROR_USER_CUSTOM_FIELD_INVALID_DATA' => 'La création du champ personnalisé a échoué. Veuillez vérifier les paramètres d\'entrée',
        'ERROR_USER_CUSTOM_FIELD_NOT_FOUND' => 'Le champ personnalisé de l\'utilisateur demandé n\'existe pas',
        'ERROR_USER_INVALID_DATA' => 'La création de l\'utilisateur a échoué. Veuillez vérifier les paramètres d\'entrée',
        'ERROR_USER_SKILL_NOT_FOUND' => 'Les compétences requises pour l\'utilisateur n\'existent pas',
        'ERROR_SLIDER_IMAGE_UPLOAD' => 'Impossible de télécharger l\'image du curseur',
        'ERROR_SLIDER_INVALID_DATA' => 'Données d\'entrée non valides',
        'ERROR_SLIDER_LIMIT' => 'Désolé, vous ne pouvez pas ajouter plus de '.config('constants.SLIDER_LIMIT').' diapositives!',
        'ERROR_NOT_VALID_EXTENSION' => 'Le fichier doit avoir le type .scss',
        'ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE' => 'Le nom du fichier ne correspond pas à la structure',
        'ERROR_INVALID_IMAGE_URL' => 'L\'URL doit être un fichier de type: jpeg, png, jpg',
        
        // Custom error code for CMS Module - 300000 - 309999
        'ERROR_INVALID_ARGUMENT' => 'Argument invalide',
        'ERROR_FOOTER_PAGE_NOT_FOUND' => 'Page de pied de page introuvable dans le système',
        'ERROR_DATABASE_OPERATIONAL' => 'Erreur opérationnelle de la base de données',
        'ERROR_NO_DATA_FOUND' => 'Aucune donnée disponible',
        'ERROR_NO_DATA_FOUND_FOR_SLUG' => 'Aucune donnée trouvée pour slug',        
        'ERROR_POLICY_PAGE_NOT_FOUND' => 'Page de stratégie introuvable dans le système',

        // Custom error code for Mission Module - 400000 - 409999
        'ERROR_INVALID_MISSION_APPLICATION_DATA' => 'Données d\'application non valides ou paramètre manquant',
        'ERROR_INVALID_MISSION_DATA' => 'Données de mission non valides ou paramètre manquant',
        'ERROR_MISSION_NOT_FOUND' => 'La mission demandée n\'existe pas',
        'ERROR_MISSION_DELETION' => 'La suppression de la mission a échoué',
        'ERROR_MISSION_REQUIRED_FIELDS_EMPTY' => 'La création de mission a échoué. Veuillez vérifier les paramètres d\'entrée',
        'ERROR_NO_MISSION_FOUND' => 'La mission ne se trouve pas dans le système',
        'ERROR_THEME_INVALID_DATA' => 'La création du thème de la mission a échoué. Veuillez vérifier les paramètres d\'entrée',
        'ERROR_THEME_NOT_FOUND' => 'Le thème de la mission ne figure pas dans le système',
        'ERROR_SKILL_NOT_FOUND' => 'La compétence ne se trouve pas dans le système',
        'ERROR_INVALID_MISSION_ID' => 'Identifiant de mission invalide',
        'ERROR_MISSION_APPLICATION_SEATS_NOT_AVAILABLE' => 'Aucune place disponible pour cette mission',
        'ERROR_MISSION_APPLICATION_DEADLINE_PASSED' => 'La date limite de candidature est passée. Vous ne pouvez pas postuler pour cette mission',
        'ERROR_MISSION_APPLICATION_ALREADY_ADDED' => 'Vous avez déjà postulé pour cette mission',
        'ERROR_MISSION_APPLICATION_NOT_FOUND' => 'L\'application de mission demandée n\'existe pas',
        'ERROR_MISSION_RATING_INVALID_DATA' => 'La création de la note de mission a échoué. Veuillez vérifier les paramètres d`entrée',
        'ERROR_MISSION_COMMENT_INVALID_DATA' => 'La création de commentaire de mission a échoué. Veuillez vérifier les paramètres d`entrée',
        'SKILL_LIMIT' => 'Désolé, vous ne pouvez pas ajouter plus de '. config('constants.SKILL_LIMIT').' compétences',

        'ERROR_INVALID_INVITE_MISSION_DATA' => 'Détail de la mission d\'invitation non valide',
        'ERROR_INVITE_MISSION_ALREADY_EXIST' => 'L\'utilisateur est déjà invité pour cette mission',
        
        // Custom error code for Tenant Authorization - 210000 - 219999
        'ERROR_INVALID_API_AND_SECRET_KEY' => 'Clé API ou clé secrète invalide',
        'ERROR_API_AND_SECRET_KEY_REQUIRED' => 'Clé API et clé secrète requises',
        'ERROR_EMAIL_NOT_EXIST' => 'L\'adresse email n\'existe pas dans le système',
        'ERROR_INVALID_RESET_PASSWORD_LINK' => 'Le lien de réinitialisation du mot de passe a expiré ou n\'est pas valide',
        'ERROR_RESET_PASSWORD_INVALID_DATA' => 'Données d\'entrée non valides',
        'ERROR_SEND_RESET_PASSWORD_LINK' => 'Une erreur s\'est produite lors de l\'envoi du lien de réinitialisation du mot de passe.',
        'ERROR_INVALID_DETAIL' => 'Mot de passe de réinitialisation ou adresse électronique non valide',
        'ERROR_INVALID_PASSWORD' => 'Mot de passe incorrect',
        'ERROR_TENANT_DOMAIN_NOT_FOUND' => 'Domaine locataire non trouvé',
        'ERROR_TOKEN_EXPIRED' => 'Le jeton fourni a expiré',
        'ERROR_IN_TOKEN_DECODE' => 'Une erreur lors du décodage du jeton',
        'ERROR_TOKEN_NOT_PROVIDED' => 'Jeton non fourni',
        

        // Custom error code for common exception
        'ERROR_OCCURRED' => 'Une erreur est survenue',
        'ERROR_INVALID_JSON' => 'Format Json invalide',
        
        // Custom erro code for other errors - 800000 - 809999
        'ERROR_ON_UPDATING_STYLING_VARIBLE_IN_DATABASE' => 'Une erreur est survenue lors de la mise à jour des couleurs dans la base de données.',
        'ERROR_WHILE_DOWNLOADING_FILES_FROM_S3_TO_LOCAL' => 'Échec du téléchargement du fichier de S3 au local',
        'ERROR_WHILE_COMPILING_SCSS_FILES' => 'Une erreur s\'est produite lors de la compilation des fichiers SCSS pour mettre à jour les modifications SCSS.',
        'ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL' => 'Une erreur est survenue lors du stockage du fichier CSS compilé sur le stockage local.',
        'ERROR_NO_FILES_FOUND_TO_UPLOAD_ON_S3_BUCKET' => 'Aucun fichier trouvé à télécharger sur le compartiment s3',
        'ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3' => 'Échec du téléchargement de fichiers sur S3',
        'ERROR_FAILED_TO_RESET_STYLING' => 'Échec de la réinitialisation des paramètres de style',
        'ERROR_DEFAULT_THEME_FOLDER_NOT_FOUND' => 'Dossier de thème par défaut introuvable sur le serveur',
        'ERROR_NO_FILES_FOUND_TO_DOWNLOAD' => 'Aucun fichier d\'actif trouvé sur S3 pour le locataire',
        'ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3' => 'Dossier du locataire non trouvé',
        'ERROR_NO_FILES_FOUND_IN_ASSETS_FOLDER' => 'Aucun fichier trouvé dans le dossier des actifs S3 pour ce locataire',
        'ERROR_BOOSTRAP_SCSS_NOT_FOUND' => 'Fichier SCSS d\'amorçage introuvable lors de la compilation des fichiers SCSS',
        'ERROR_SETTING_FOUND' => 'Paramètre non trouvé',
        'ERROR_IMAGE_FILE_NOT_FOUND_ON_S3' => 'Fichier image introuvable sur le serveur S3',
        'ERROR_WHILE_UPLOADING_IMAGE_ON_S3' => 'Une erreur lors du téléchargement de l\'image sur S3',
        'ERROR_DOWNLOADING_IMAGE_TO_LOCAL' => 'Une erreur lors du téléchargement de l\'image de S3 sur le serveur',
        'ERROR_IMAGE_UPLOAD_INVALID_DATA' => 'Fichier d\'entrée invalide',
        'ERROR_TENANT_OPTION_NOT_FOUND' => 'Aucune option de locataire trouvée',
        'ERROR_LANGUAGE_NOT_FOUND' => 'Langue non trouvée',
        'ERROR_FAILED_TO_RESET_ASSET_IMAGE' => 'Impossible de réinitialiser les images d\'actif'
    ],
];

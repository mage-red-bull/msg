<?php

/**
 * Class Constants
 * Regroupe l'ensemble des constantes.
 */
class Constants {
    /**
     *  Taille maximale du message, en réalité, on peut accepter 501 caractères maximum,
     * mais on arrondit.
     */
    const MESSAGE_SIZE_MAX = 500;

    /**
     * Chemin vers la clé publique.
     */
    const PUBLIC_KEY_PATH = './include/rsa_4096_public.pem';
}
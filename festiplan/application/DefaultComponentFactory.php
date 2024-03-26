<?php

namespace application;

use controleurs\HomeController;
use controleurs\FestivalControleur;
use controleurs\SpectacleControleur;
use controleurs\AccueilControleur;
use controleurs\UtilisateurCompteControleur;
use controleurs\GrijControleur;
use modeles\UserModele;
use modeles\SpectacleModele;
use modeles\FestivalModele;
use modeles\GrijModele;
use yasmf\ComponentFactory;
use yasmf\NoControllerAvailableForNameException;
use yasmf\NoServiceAvailableForNameException;

class DefaultComponentFactory implements ComponentFactory 
{

    private ?UserModele $userModele = null;

    private ?SpectacleModele $spectacleModele = null;

    private ?FestivalModele $festivalModele = null;

    private ?GrijModele $grijModele = null;

    public function buildControllerByName(string $controller_name): mixed {
        return match ($controller_name) {
            "Home" => $this->buildHomeController(),
            "Accueil" => $this->buildAccueilController(),
            "Spectacle" => $this->buildSpectacleController(),
            "Festival" => $this->buildFestivalController(),
            "UtilisateurCompte" => $this->buildUtilisateurCompteController(),
            "Grij" => $this->buildGrijController(),
            default => throw new NoControllerAvailableForNameException($controller_name)
        };
    }

    public function buildServiceByName(string $service_name): mixed
    {
        return match ($service_name){
            "User" => $this->buildUserModele(),
            "Spectacle" => $this->buildSpectacleModele(),
            "Festival" => $this->buildFestivalModele(),
            "Grij" => $this->buildGrijModele(),
            default => throw new NoServiceAvailableForNameException($service_name)
        };
    }

    private function buildHomeController(): HomeController
    {
        return new HomeController($this->buildServiceByName("Spectacle"),$this->buildServiceByName("Festival"));
    }

    private function buildAccueilController(): AccueilControleur
    {
        return new AccueilControleur($this->buildServiceByName("Spectacle"),$this->buildServiceByName("Festival"));
    }
    
    private function buildSpectacleController(): SpectacleControleur
    {
        return new SpectacleControleur($this->buildServiceByName("Spectacle"),$this->buildServiceByName("Festival"));
    }
    
    private function buildFestivalController(): FestivalControleur
    {
        return new FestivalControleur($this->buildServiceByName("Spectacle"),$this->buildServiceByName("Festival"));
    }

    private function buildUtilisateurCompteController() : UtilisateurCompteControleur
    {
        return new UtilisateurCompteControleur($this->buildServiceByName("User"),$this->buildServiceByName("Spectacle"),$this->buildServiceByName("Festival"));
    }
    
    private function buildGrijController() : GrijControleur
    {
        return new GrijControleur($this->buildServiceByName("Grij"));
    }

   

    private function buildUserModele() : UserModele
    {
        if ($this->userModele == null) {
            $this->userModele = new UserModele();
        }
        return $this->userModele;
    }

    private function buildSpectacleModele() : SpectacleModele
    {
        if ($this->spectacleModele == null) {
            $this->spectacleModele = new SpectacleModele();
        }
        return $this->spectacleModele;
    }

    private function buildFestivalModele() : FestivalModele
    {
        if ($this->festivalModele == null) {
            $this->festivalModele = new FestivalModele();
        }
        return $this->festivalModele;
    }

    private function buildGrijModele() : GrijModele
    {
        if ($this->grijModele == null)
        {
            $this->grijModele = new GrijModele();
        }
        return $this->grijModele;
    }
    
}
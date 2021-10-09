# Vevi
### Projet de BDD2 (2ème module de Base de Données) de 1ère année de DUT

Ceci est le premier site web avec backend que j'ai réalisé, avec la collaboration de [Dorian VIDAL](https://github.com/DorianVidal).

L'objectif était de développer un clone simplifié de Twitter avec quelques contraintes parmis lesquelles:
- la présence d'un système d'authentification;
- on devait pouvoir poster / editer / supprimer des posts;
- suivre d'autres utilisateurs;
- aimer les publications des autres utilisateurs;

Le Framework PHP utilisé a été codé en cours.

Ce projet a servi d'évaluation au module de BDD2.


### Mise en place
Sur une machine avec Docker, et son utilitaire *docker-compose* d'installés, tapez 
```
docker-compose up -d
```

Cela démarrera trois conteneurs contenant respectivement un serveur apache avec php, une instance de MariaDB et une instance de phpMyAdmin.

Lors de la première utilisation, vous devez initialiser la base de données (attention, il se peut que quelques secondes soient nécessaire le temps que la MariaDB se mette en place).
Pour ce faire, connectez-vous sur phpMyAdmin (mappé sur localhost:8080) par défaut.
Entrez *root* pour le nom d'utilisateur et *mdp1234* pour le mot de passe 
(modifiable dans le fichier [docker-compose.yaml](https://github.com/corentin703/IUT-Vevi/blob/master/docker-compose.yaml).
Sélectionnez la base de donnée *vevi* dans le menu latéral et, dans le menu SQL, collez le contenu du fichier 
[Base de données vierge - Vévi.sql](https://github.com/corentin703/IUT-Vevi/blob/master/Base%20de%20donn%C3%A9es%20vierge%20-%20V%C3%A9vi.sql) et exécutez.
Après cela, la base de donnée est prête.

Le site est mappé sur le port 80 par défaut : tapez donc localhost dans la barre d'adresse pour utiliser le site.

Par défaut, la base de données utilise un volume situé dans le dossier *database_persist* situé à la racine du dépôt pour la persistance.


Pour mettre le déploiement hors service, tapez :
```
docker-compose down
```

### Formulaire de connexion
<div>
  <img src="https://raw.githubusercontent.com/corentin703/Vevi/master/ReadMe/Login.png" width=30%"/>
</div>

### Formulaire d'inscription                                                                                                             
<div>                                                                                                                      
  <img src="https://raw.githubusercontent.com/corentin703/Vevi/master/ReadMe/Register.png" width=30%"/>
</div>

### Menu principal: 
<div>
  <img src="https://raw.githubusercontent.com/corentin703/Vevi/master/ReadMe/Home.png" width=30%"/>
  <img src="https://raw.githubusercontent.com/corentin703/Vevi/master/ReadMe/AboutMe.png" width=30%"/>
</div>


### Projet fait par : 
- [Corentin VÉROT](https://github.com/corentin703)
- [Dorian VIDAL](https://github.com/DorianVidal)

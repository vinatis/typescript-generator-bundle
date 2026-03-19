# TypeScriptGeneratorBundle

Ce bundle génère des éléments TypeScript basés sur un projet Symfony.

# Installation

```bash
composer require vinatis/typescript-generator
```

> PHP *>=8.2* — Symfony *^7.0*

# Commandes disponibles

- [Générer des interfaces](#générer-des-interfaces)
- [Générer un package](#générer-un-package)
- [Tout générer](#tout-générer)

---

## Générer des interfaces

Cette fonctionnalité permet de créer des interfaces TypeScript à partir de classes PHP conçues pour fonctionner comme des entités Doctrine.

Les interfaces sont générées en se basant sur les propriétés de ces classes. Il existe 3 façons d'obtenir le type de chaque propriété :

* Typage fort de la propriété (disponible depuis PHP 7.4)
  * > `private int $id;`
* Typage dans le commentaire de la propriété
  * > `@var int`
* Typage via les annotations ou attributs Doctrine
  * > `@ORM\Column(type="integer")` ou `#[ORM\Column(type: 'integer')]`

Si aucun type n'est trouvé, l'interface sera générée avec le type `unknown`.

---

La génération des interfaces s'effectue avec la commande suivante :

```bash
bin/console typescript:generate:interface output-dir [entities-dir]
```

Ce commande accepte 2 paramètres, dont un obligatoire et un optionnel.

**output-dir** *(Obligatoire)* : Répertoire où les interfaces seront créées.
**entities-dir** *(Optionnel)* : Répertoire des entités à utiliser pour générer les interfaces. Par défaut : `src/Entity/`.

Pour qu'une entité soit convertie en interface, il est nécessaire d'ajouter le commentaire `#TypeScriptMe` ou l'attribut `#[TypeScriptMe]` dans la définition de la classe. Exemple :

```php
<?php
namespace App\Entity;

// Avec annotation (ancienne syntaxe)
/**
 * #TypeScriptMe
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    // ...
}
```

```php
<?php
namespace App\Entity;

// Avec attribut PHP 8 (syntaxe recommandée avec Symfony 7)
#[TypeScriptMe]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    // ...
}
```

### Types supportés

| TypeScript | PHP / Doctrine |
|---|---|
| `number` | int, integer, smallint, bigint, decimal, float |
| `string` | string, text, guid, date, time, datetime, datetimetz |
| `boolean` | boolean |
| `Interface` | Interface liée dans une relation one-to-one |
| `Interface[]` | Tableau d'interfaces dans une relation one-to-many |
| `unknown` | Tout autre type non reconnu |

> Si les annotations Doctrine définissent `nullable=true`, ou si le typage PHP utilise `?` avant le type, la propriété sera marquée comme optionnelle (`?`) dans l'interface TypeScript générée.

### Exemple

Entité PHP avec attributs Symfony 7 / Doctrine :

```php
// src/Entity/User.php
<?php

namespace App\Entity;

#[TypeScriptMe]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $lastname;

    #[ORM\OneToMany(targetEntity: Factory::class, mappedBy: 'author')]
    private \Doctrine\Common\Collections\Collection $factories;

    #[ORM\OneToOne(targetEntity: Photo::class, mappedBy: 'user')]
    private ?Photo $photo;

    // ...
}
```

Interface TypeScript générée :

```typescript
// interfaces/User.ts

export interface User {
  id: number,
  name: string,
  lastname?: string,
  photo: Photo,
  factories: Factory[]
}
```

Pour faciliter l'utilisation des interfaces, le fichier `models.d.ts` est généré automatiquement avec l'export de toutes les interfaces :

```typescript
// interfaces/models.d.ts

export * from './User';
export * from './Photo';
export * from './Factory';
```

---

## Générer un package

```bash
bin/console typescript:generate:package output-dir [package-name] [version]
```

Cette commande génère un fichier `package.json` avec les données de base pour publier dans un dépôt npm privé.

À chaque exécution, la version **patch** est incrémentée par défaut. Il est possible de passer une version spécifique ou d'indiquer `patch`, `minor` ou `major`.

Exemple de `package.json` généré :

```json
// interfaces/package.json
{
    "name": "@mon-org/mon-projet",
    "version": "0.0.1",
    "description": "typescript interfaces for @mon-org/mon-projet",
    "types": "models.d.ts",
    "keywords": [],
    "author": "",
    "license": "EUPL"
}
```

> [Bibliothèque utilisée pour la gestion des versions](https://github.com/PHLAK/SemVer)

---

## Tout générer

```bash
bin/console typescript:generate:all output-dir [entities-dir] [package-name] [version]
```

Exécute les deux commandes précédentes en une seule fois.

---

### Publier dans un dépôt npm privé

Pour publier dans un dépôt privé, il faut avoir préalablement généré le fichier `package.json` et [avoir npm installé](https://github.com/nvm-sh/nvm#installing-and-updating).

1) Se connecter à npm

```bash
npm adduser --registry https://npm.exemple.com
```

2) Publier / mettre à jour les interfaces

```bash
npm publish --registry https://npm.exemple.com
```

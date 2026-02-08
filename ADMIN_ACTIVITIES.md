# Admin : Activités, catégories, guides et horaires

Ce guide explique comment ajouter des activités, catégories, guides et horaires pour que le **blog activités** (page front) affiche du contenu.

---

## 1. Où aller (URLs)

Après avoir démarré le serveur (`symfony server:start` ou `php -S localhost:8000 -t public`), ouvrez :

| Page | URL |
|------|-----|
| **Catégories** | `/activity/category` |
| **Guides** | `/guide` |
| **Activités** | `/activity` |
| **Horaires** | `/activity/schedule` |
| **Blog (front)** | `/activity/activities` |

Sur chaque page d’index (Catégories, Guides, Activités, Horaires), une barre de navigation **Admin** en haut permet de passer de l’une à l’autre et d’aller au blog.

---

## 2. Ordre à respecter

Pour avoir des activités visibles sur le blog, il faut créer les données dans cet ordre :

1. **Catégories** → une activité doit avoir une catégorie.
2. **Guides** (optionnel) → vous pouvez créer des activités sans guide.
3. **Activités** → vous choisissez une catégorie (et éventuellement un guide).
4. **Horaires** → chaque horaire est attaché à une activité (date/heure de début, fin, nombre de places).

Le filtre du blog attend notamment ces catégories (si elles existent en base) : **Camping**, **Équitation**, **Kayak**, **Randonnée**, **Yoga**. Vous pouvez créer exactement ces noms pour que les filtres fonctionnent.

---

## 3. Étapes détaillées

### Étape 1 : Créer des catégories

1. Aller sur **`/activity/category`**.
2. Cliquer sur **« Créer une catégorie »**.
3. Renseigner au minimum le **Nom** (ex. : Camping, Équitation, Kayak, Randonnée, Yoga). Description et icône sont optionnels.
4. Enregistrer. Répéter pour chaque catégorie souhaitée.

### Étape 2 : Créer des guides (optionnel)

1. Aller sur **`/guide`**.
2. Cliquer sur **« Créer un guide »**.
3. Renseigner prénom, nom, email, téléphone. Bio, note, photo sont optionnels.
4. Enregistrer.

### Étape 3 : Créer des activités

1. Aller sur **`/activity`**.
2. Cliquer sur **« Créer une activité »**.
3. Renseigner : titre, description, prix, durée (minutes), lieu, nombre max de participants, image (nom du fichier si vous uploadez), actif (oui/non).
4. Choisir une **catégorie** dans la liste (les noms s’affichent).
5. Choisir un **guide** dans la liste ou laisser vide.
6. Enregistrer.

### Étape 4 : Créer des horaires

1. Aller sur **`/activity/schedule`**.
2. Cliquer sur **« Créer un horaire »**.
3. Choisir l’**activité** dans la liste (les titres s’affichent).
4. Renseigner date/heure de **début**, date/heure de **fin**, nombre de **places disponibles**.
5. Enregistrer. Vous pouvez créer plusieurs horaires pour la même activité.

---

## 4. Vérifier le résultat

- Aller sur **`/activity/activities`** (blog front).
- Vous devez voir les activités avec le filtre par catégorie et par prix. Cliquer sur une carte ouvre la fiche de l’activité.

---

## 5. Résumé des templates CRUD

- **activity** : `templates/activity/` (index, new, edit, show, _form, _delete_form)
- **activity_category** : `templates/activity_category/`
- **activity_schedule** : `templates/activity_schedule/`
- **guide** : `templates/guide/`

Toutes les pages d’index et de formulaire (new/edit) utilisent le layout du site et la barre **Admin** pour naviguer entre Catégories, Guides, Activités, Horaires et le blog.

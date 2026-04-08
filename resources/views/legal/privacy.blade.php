<x-layouts.app :title="'Confidentialite - Wiki Builder API'">
    <section class="panel card">
        <span class="pill">Confidentialite</span>
        <div>
            <h1 style="font-size:38px;line-height:1;">Politique de confidentialite</h1>
            <p class="helper" style="margin-top:10px;">Version {{ $policyVersion }}. Cette page explique quelles donnees sont traitees, pourquoi elles le sont et comment exercer tes droits sur le service.</p>
        </div>
    </section>

    <section class="grid">
        <article class="panel card">
            <h2>Donnees traitees</h2>
            <div class="list">
                <article>
                    <strong>Compte utilisateur</strong>
                    <p class="helper">Email, username, mot de passe chiffre, nom affiche et metadonnees de connexion utiles au fonctionnement de l'espace utilisateur.</p>
                </article>
                <article>
                    <strong>Contenus generes</strong>
                    <p class="helper">Builds, jeux crees, brouillons et parametres associes a ton compte.</p>
                </article>
                <article>
                    <strong>Consentements</strong>
                    <p class="helper">Date d'acceptation de cette politique et consentement marketing optionnel lorsqu'il est donne.</p>
                </article>
            </div>
        </article>

        <article class="panel card">
            <h2>Finalites</h2>
            <div class="list">
                <article>
                    <strong>Fournir le service</strong>
                    <p class="helper">Creer un compte, maintenir une session, proteger l'acces et rattacher les brouillons et builds a la bonne personne.</p>
                </article>
                <article>
                    <strong>Securiser l'application</strong>
                    <p class="helper">Limiter les tentatives de connexion, journaliser les dates techniques utiles et proteger les actions sensibles.</p>
                </article>
                <article>
                    <strong>Respecter tes droits</strong>
                    <p class="helper">Mettre a disposition un export des donnees, l'anonymisation et la suppression du compte.</p>
                </article>
            </div>
        </article>
    </section>

    <section class="grid">
        <article class="panel card">
            <h2>Exercer tes droits</h2>
            <p class="helper">Depuis la page Mon compte, tu peux :</p>
            <div class="list">
                <article>
                    <strong>Acceder a tes donnees</strong>
                    <p class="helper">Exporter un JSON lisible contenant les donnees rattachees a ton compte.</p>
                </article>
                <article>
                    <strong>Rectifier ou supprimer</strong>
                    <p class="helper">Mettre a jour les donnees utiles a ton compte ou demander l'anonymisation / suppression lorsque c'est approprie.</p>
                </article>
                <article>
                    <strong>Nous contacter</strong>
                    <p class="helper">Contact de reference : {{ $contactEmail }}</p>
                </article>
            </div>
        </article>

        <article class="panel card">
            <h2>Principes appliques</h2>
            <div class="list">
                <article>
                    <strong>Minimisation</strong>
                    <p class="helper">Le formulaire d'inscription ne collecte que le strict necessaire pour creer le compte.</p>
                </article>
                <article>
                    <strong>Protection par defaut</strong>
                    <p class="helper">Les actions sensibles sont protegees par session web, jeton CSRF, validation serveur et confirmation du mot de passe.</p>
                </article>
                <article>
                    <strong>Transparence</strong>
                    <p class="helper">Cette page et l'espace Mon compte restent accessibles pour comprendre le traitement et piloter tes droits.</p>
                </article>
            </div>
        </article>
    </section>
</x-layouts.app>

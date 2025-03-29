<?php
namespace App\View\Welcome;

use App\View\Components\Layout\NavbarView;

class WelcomeView {
    public static function render() {
        ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BorrowMyStuff - Plateforme de prêt d'objets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#000000',
                        secondary: '#FFFFFF'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        html {
            font-size: 18px; /* Augmentation de la taille de base de la police */
        }
        
        .gradient-text {
            background: linear-gradient(to right, #000000, #333333);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .feature-card {
            transition: transform 0.2s ease-in-out;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="font-sans bg-white">
    <?= NavbarView::render() ?>

    <!-- Hero Section -->
    <section class="pt-32 pb-24 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-6xl md:text-8xl font-light mb-10 gradient-text leading-tight">
                Empruntez. Partagez.<br>Connectez.
            </h1>
            <p class="text-2xl text-gray-600 mb-14 font-light max-w-3xl mx-auto leading-relaxed">
                Découvrez une nouvelle façon de partager vos objets avec votre communauté.
                Simple, sécurisé et collaboratif.
            </p>
            <div class="flex flex-col sm:flex-row gap-8 justify-center">
                <a href="/register" class="inline-flex items-center justify-center rounded-xl bg-black px-8 py-4 text-base font-semibold text-white shadow-sm transition-all duration-150 hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Commencer gratuitement
                </a>
                <a href="#features" class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 transition-all duration-150 hover:bg-gray-50">
                    En savoir plus
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-5xl font-light text-center mb-20">Comment ça marche ?</h2>
            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-10 rounded-xl shadow-sm">
                    <span class="material-icons-outlined text-5xl mb-6">search</span>
                    <h3 class="text-2xl font-medium mb-4">Trouvez</h3>
                    <p class="text-lg text-gray-600 font-light leading-relaxed">
                        Parcourez notre catalogue d'objets disponibles dans votre communauté.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="feature-card bg-white p-10 rounded-xl shadow-sm">
                    <span class="material-icons-outlined text-5xl mb-6">handshake</span>
                    <h3 class="text-2xl font-medium mb-4">Empruntez</h3>
                    <p class="text-lg text-gray-600 font-light leading-relaxed">
                        Faites une demande de prêt et convenez des détails avec le propriétaire.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="feature-card bg-white p-10 rounded-xl shadow-sm">
                    <span class="material-icons-outlined text-5xl mb-6">inventory_2</span>
                    <h3 class="text-2xl font-medium mb-4">Partagez</h3>
                    <p class="text-lg text-gray-600 font-light leading-relaxed">
                        Mettez vos propres objets à disposition de la communauté.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Home Page Showcase -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-5xl font-light text-center mb-20">Explorez notre catalogue</h2>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-3xl font-light mb-8">Une expérience de recherche intuitive</h3>
                    <ul class="space-y-6">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">search</span>
                            <span class="text-lg font-light leading-relaxed">Barre de recherche intelligente pour trouver rapidement ce dont vous avez besoin</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">grid_view</span>
                            <span class="text-lg font-light leading-relaxed">Affichage en grille des objets disponibles avec images et détails</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">filter_alt</span>
                            <span class="text-lg font-light leading-relaxed">Filtres avancés pour affiner votre recherche</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">info</span>
                            <span class="text-lg font-light leading-relaxed">Détails complets de chaque objet en un clic</span>
                        </li>
                    </ul>
                    <div class="mt-10">
                        <a href="/home" class="inline-flex items-center justify-center rounded-xl bg-black px-8 py-4 text-base font-semibold text-white shadow-sm transition-all duration-150 hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span class="material-icons-outlined mr-3 text-2xl">explore</span>
                            Explorer le catalogue
                        </a>
                    </div>
                </div>
                <div class="order-1 md:order-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-black/5 to-white/5 rounded-xl"></div>
                        <img src="/public/images/home-preview.png" alt="Page d'accueil" class="rounded-xl shadow-lg w-full">
                        <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-sm rounded-lg p-6 shadow-lg">
                            <div class="flex items-center">
                                <span class="material-icons-outlined text-2xl text-black mr-3">auto_awesome</span>
                                <span class="text-lg font-light">Découvrez les objets disponibles près de chez vous</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshot Section -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-5xl font-light text-center mb-20">Une interface simple et intuitive</h2>
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <img src="/public/images/dashboard-preview.png" alt="Dashboard Preview" class="rounded-xl shadow-lg w-full">
                </div>
                <div>
                    <h3 class="text-3xl font-light mb-8">Gérez vos prêts facilement</h3>
                    <ul class="space-y-6">
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">check_circle</span>
                            <span class="text-lg font-light leading-relaxed">Tableau de bord intuitif pour suivre vos prêts</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">check_circle</span>
                            <span class="text-lg font-light leading-relaxed">Système de notifications pour les demandes</span>
                        </li>
                        <li class="flex items-start">
                            <span class="material-icons-outlined text-3xl text-black mr-4">check_circle</span>
                            <span class="text-lg font-light leading-relaxed">Historique complet des transactions</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-black text-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-5xl font-light mb-10">Prêt à rejoindre la communauté ?</h2>
            <p class="text-2xl font-light mb-14 max-w-3xl mx-auto leading-relaxed">
                Créez votre compte gratuitement et commencez à partager avec votre communauté dès aujourd'hui.
            </p>
            <a href="/register" class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-4 text-base font-semibold text-gray-900 shadow-sm transition-all duration-150 hover:bg-gray-50">
                Créer un compte
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-10">
                <div>
                    <h4 class="text-base font-medium mb-6">À propos</h4>
                    <p class="text-base text-gray-600 font-light leading-relaxed">
                        BorrowMyStuff est une plateforme de prêt d'objets qui connecte les membres de votre communauté.
                    </p>
                </div>
                <div>
                    <h4 class="text-base font-medium mb-6">Liens rapides</h4>
                    <ul class="space-y-4">
                        <li><a href="/login" class="text-base text-gray-600 hover:text-black font-light">Se connecter</a></li>
                        <li><a href="/register" class="text-base text-gray-600 hover:text-black font-light">S'inscrire</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base font-medium mb-6">Support</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-base text-gray-600 hover:text-black font-light">FAQ</a></li>
                        <li><a href="#" class="text-base text-gray-600 hover:text-black font-light">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-base font-medium mb-6">Légal</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-base text-gray-600 hover:text-black font-light">Conditions d'utilisation</a></li>
                        <li><a href="#" class="text-base text-gray-600 hover:text-black font-light">Politique de confidentialité</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-16 pt-10 border-t border-gray-200 text-center">
                <p class="text-base text-gray-600 font-light">
                    © <?= date('Y') ?> BorrowMyStuff. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
<?php
        return ob_get_clean();
    }
}

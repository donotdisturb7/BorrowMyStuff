<?php

namespace App\View\Login;

class SignInView {
    public static function render($errors = []) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        ob_start();
?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Connexion - BorrowMyStuff</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: '#000000',
                                secondary: '#FFFFFF'
                            }
                        }
                    }
                }
            </script>
        </head>
        <body class="bg-white min-h-screen">
            <div class="flex min-h-screen">
                <!-- Left side - Image/Branding -->
                <div class="hidden lg:flex lg:w-1/2 bg-gray-100 p-12 flex-col justify-between">
                    <div>
                        <h1 class="text-4xl font-light text-black mb-6">BorrowMyStuff</h1>
                        <p class="text-gray-700 text-xl max-w-md">Partagez et empruntez des objets de votre communauté en toute simplicité.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <img src="https://images.unsplash.com/photo-1579389083078-4e7018379f7e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=870&q=80" 
                            alt="Personnes partageant des objets" class="border border-gray-200 max-w-md rounded-lg">
                        <div class="mt-8 text-gray-700 text-center">
                            <p class="text-lg">"BorrowMyStuff m'a aidé à économiser de l'argent et à me connecter avec mes voisins !"</p>
                            <p class="mt-2 font-medium">- Sarah Johnson</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right side - Login Form -->
                <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
                    <div class="w-full max-w-md">
                        <div class="text-center mb-10">
                            <h2 class="text-3xl font-light text-black">Bon retour parmi nous !</h2>
                            <p class="text-gray-600 mt-2">Connectez-vous à votre compte pour continuer</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="border border-gray-300 text-red-700 p-3 mb-6 rounded-lg">
                                <?php foreach ($errors as $error): ?>
                                    <p><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="./login" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="relative">
                              <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Email</p>
                              <input placeholder="your@email.com" type="email" id="email" name="email" required class="border placeholder-gray-400 focus:outline-none
                                  focus:border-black w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                  border-gray-300 rounded-md"/>
                            </div>
                            
                            <div class="relative">
                              <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Mot de passe</p>
                              <input placeholder="Mot de passe" type="password" id="password" name="password" required class="border placeholder-gray-400 focus:outline-none
                                  focus:border-black w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                  border-gray-300 rounded-md"/>
                            </div>
                            
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-black focus:ring-black border-gray-300">
                                    <label for="remember" class="ml-2 block text-sm text-gray-700">Se souvenir de moi</label>
                                </div>
                                
                                <a href="#" class="text-sm font-medium text-black hover:text-gray-600 border-b border-black">
                                    Mot de passe oublié?
                                </a>
                            </div>
                            
                            <div class="relative">
                              <button type="submit" class="w-full inline-block pt-4 pr-5 pb-4 pl-5 text-xl font-medium text-center text-white bg-primary
                                    rounded-lg transition duration-200 hover:bg-primary/90 ease shadow-md hover:shadow-lg">Se connecter</button>
                            </div>
                            
                            <div class="relative">
                              <p class="text-center text-gray-600">
                                Vous n'avez pas de compte ? <a href="./register" class="text-black font-medium border-b border-black hover:text-gray-800">S'inscrire</a>
                              </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </body>
        </html>
<?php
        return ob_get_clean();
    }
}
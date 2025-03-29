<?php

namespace App\View\Login;

class SignUpView {
    public static function render($errors = []) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        ob_start();
?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Inscription - BorrowMyStuff</title>
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
                <!-- Right side - Registration Form -->
                <div class="w-full lg:w-1/2 flex items-center justify-center p-8 order-2 lg:order-1">
                    <div class="w-full max-w-md">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-light text-black">Créer un compte</h2>
                            <p class="text-gray-600 mt-2">Rejoignez notre communauté et partagez vos objets</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="border border-gray-300 text-red-700 p-3 mb-6">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php if (is_array($fieldErrors)): ?>
                                        <?php foreach ($fieldErrors as $error): ?>
                                            <p><?= htmlspecialchars($error) ?></p>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p><?= htmlspecialchars($fieldErrors) ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="./register" class="space-y-5">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="relative">
                                <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Nom d'utilisateur</p>
                                <input placeholder="JohnDoe" type="text" id="username" name="username" required class="border placeholder-gray-400 focus:outline-none
                                    focus:border-black w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                    border-gray-300"/>
                            </div>
                            
                            <div class="relative">
                                <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Email</p>
                                <input placeholder="example@email.com" type="email" id="email" name="email" required class="border placeholder-gray-400 focus:outline-none
                                    focus:border-black w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                    border-gray-300"/>
                            </div>
                            
                            <div class="relative">
                                <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Mot de passe</p>
                                <input placeholder="Mot de passe" type="password" id="password" name="password" required class="border placeholder-gray-400 focus:outline-none
                                    focus:border-black w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                    border-gray-300"/>
                            </div>
                            
                            <div class="relative">
                                <p class="bg-white pt-0 pr-2 pb-0 pl-2 -mt-3 mr-0 mb-0 ml-2 font-medium text-gray-600 absolute">Confirmer le mot de passe</p>
                                <input placeholder="Confirmer le mot de passe" type="password" id="confirm_password" name="confirm_password" required class="border placeholder-gray-400 focus:outline-none
                                    focus:border-primary w-full pt-4 pr-4 pb-4 pl-4 mt-2 mr-0 mb-0 ml-0 text-base block bg-white
                                    border-gray-300 rounded-md"/>
                            </div>
                            
                            <div class="relative mt-8">
                                <button type="submit" class="w-full inline-block pt-4 pr-5 pb-4 pl-5 text-xl font-medium text-center text-white bg-primary
                                    rounded-lg transition duration-200 hover:bg-primary/90 ease shadow-md hover:shadow-lg">Créer un compte</button>
                            </div>
                            
                            <div class="relative">
                                <p class="text-center text-gray-600 mt-4">
                                    Déjà un compte ? <a href="./login" class="text-primary font-medium hover:underline">Se connecter</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Left side - Image/Branding -->
                <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-r from-primary to-purple-700 p-12 flex-col justify-between order-1 lg:order-2">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-6">BorrowMyStuff</h1>
                        <p class="text-white/80 text-xl max-w-md">Rejoignez notre communauté de partageurs et emprunteurs aujourd'hui !</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1784&q=80" 
                            alt="Community sharing" class="rounded-xl shadow-lg max-w-md">
                        <div class="mt-8 text-white/80 text-center">
                            <p class="text-lg">"Créer un compte a été facile et j'ai déjà emprunté 3 objets à mes voisins !"</p>
                            <p class="mt-2 font-semibold">- Michael Rodriguez</p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
<?php
        return ob_get_clean();
    }
}
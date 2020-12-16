<?php

namespace App\Controller;

use App\Entity\Jouer;
use App\Entity\Partie;
use App\Entity\Tchats;
use App\Entity\User;
use App\Repository\AcquerirRepository;
use App\Repository\AcquisitionsRepository;
use App\Repository\CarteRepository;
use App\Repository\CasesRepository;
use App\Repository\JouerRepository;
use App\Repository\PartieRepository;
use App\Repository\TchatsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/partie")
 */
class PartieController extends AbstractController
{
    /**
     * @Route("/test", name="partie_test")
     */
    public function testAfficherPlateau(CasesRepository $casesRepository)
    {
        $cases = $casesRepository->findBy([], ['position' => 'ASC']);

        return $this->render('partie/test.html.twig',
            [
                'cases' => $cases
            ]);
    }

    /**
     * @Route("/creer-partie", name="creer_partie")
     */
    public function creerPartie(Request $request, UserRepository $userRepository, CarteRepository $carteRepository, TchatsRepository $tchatsRepository)
    {



        if ($request->isMethod('POST')) {
            $narrateur = $userRepository->findBy(['id' => 1])[0];
            $user = $this->getUser();

            $cartes = $carteRepository->findAll();
            $tableauDeCartes = ['acqusition' => [], 'evenement' => [], 'courrier' => []];
            foreach ($cartes as $carte)
            {
                $tableauDeCartes[$carte->getTypeDeCarte()][] = $carte->getId();
            }
            shuffle($tableauDeCartes['acqusition']);
            shuffle($tableauDeCartes['evenement']);
            shuffle($tableauDeCartes['courrier']);

            $partie = new Partie();
            $partie->setPioche($tableauDeCartes);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);

            $jouer = new Jouer();
            $jouer->setPartie($partie);
            $jouer->setClassement(1);
            $jouer->setJoueur($user);
            $em->persist($jouer);


            $nouveautchat = new Tchats();
            $nouveautchat->setMessage('Une nouvelle partie de Vie de MMI commence');
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($narrateur);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);



            $nouveautchat2 = new Tchats();
            $nouveautchat2->setMessage('La partie sera prête à être lancée quand au moins un autre joueur sera présent.');
            $nouveautchat2->setPartie($partie);
            $nouveautchat2->setUser($narrateur);
            $nouveautchat2->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat2);



            $nouveautchat3 = new Tchats();
            $nouveautchat3->setMessage('Envoyez le code de la partie à vos amis pour qu\'ils puissent rejoindre la partie');
            $nouveautchat3->setPartie($partie);
            $nouveautchat3->setUser($narrateur);
            $nouveautchat3->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat3);



            $nouveautchat4 = new Tchats();
            $nouveautchat4->setMessage('Une aventure pleine de rebondissements vous attend !');
            $nouveautchat4->setPartie($partie);
            $nouveautchat4->setUser($narrateur);
            $nouveautchat4->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat4);



            $em->flush();
            return $this->redirectToRoute('affiche_code_partie', ['partie' => $partie->getId()]);
        }

        return $this->render('partie/creerpartie.html.twig',
            [
                'joueurs' => $userRepository->findAll()
            ]);
    }

    /**
     * @Route("/affiche-code-partie/{partie}", name="affiche_code_partie")
     * @param Partie $partie
     *
     * @return Response
     */
    public function afficheCodePartie(Request $request, TchatsRepository $tchatsRepository, Partie $partie)
    {
        $tchats = $tchatsRepository->findBy(['partie' => $partie] );
        $user = $this->getUser();

        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();
            $nouveautchat = new Tchats();
            $nouveautchat->setMessage($request->request->get('message'));
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($user);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);
            $em->flush();

        }



        return $this->render('partie/afficheCodePartie.html.twig',
            [
                'partie' => $partie,
                'tchats' =>$tchats
            ]);
    }

    /**
     * @Route("/fin-code-partie/{partie}", name="fin_code_partie")
     * @param Partie $partie
     *
     * @return Response
     */
    public function finCodePartie(Request $request, TchatsRepository $tchatsRepository, Partie $partie, JouerRepository $jouerRepository)
    {
        $tchats = $tchatsRepository->findBy(['partie' => $partie] );
        $user = $this->getUser();

        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();
            $nouveautchat = new Tchats();
            $nouveautchat->setMessage($request->request->get('message'));
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($user);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);
            $em->flush();

        }

        $jouers = $jouerRepository->findBy(['partie' => $partie]);
        $gagnant = $jouers[0];
        foreach ($jouers as $jouer){

            if ($jouer->getArgent()>$gagnant->getArgent()){
                $gagnant = $jouer;
            }

        }

        return $this->render('partie/finCodePartie.html.twig',
            [
                'gagnant' => $gagnant,
                'partie' => $partie,
                'tchats' =>$tchats
            ]);
    }

    /**
     * @Route("/rejoindre-partie/{partie}", name="rejoindre_partie")
     * @param Partie $partie
     *
     * @return Response
     */
    public function rejoindrePartie(Request $request, JouerRepository $jouerRepository, UserRepository $userRepository, Partie $partie, PartieRepository $partieRepository, TchatsRepository $tchatsRepository){

        $user = $this->getUser();
        $jouer = $jouerRepository->findBy(['joueur' => $user, 'partie' => $partie] );

          if (empty($jouer)){

             $testpartie = $partieRepository->findBy([ 'id' => $partie, 'etatPartie' => 'NC']);

            if (!empty($testpartie)) {

                $place = $jouerRepository->findBy(['partie' => $partie]);
                $compte = count($place);

                if ($compte < 4){

                    $em = $this->getDoctrine()->getManager();
                $jouer = new Jouer();
                $jouer->setPartie($partie);
                $jouer->setClassement($compte+1);
                $jouer->setJoueur($user);
                $em->persist($jouer);
                $em->flush();

                    $em = $this->getDoctrine()->getManager();
                    $nouveautchat = new Tchats();
                    $nouveautchat->setMessage($user->getName().' vient de rejoindre la partie.');
                    $nouveautchat->setPartie($partie);
                    $nouveautchat->setUser($user);
                    $nouveautchat->setTemps(new \DateTime('now'));
                    $em->persist($nouveautchat);
                    $em->flush();

                return $this->redirectToRoute('affiche_code_partie', ['partie' => $partie->getId()]);

            }
                else {
                    return $this->render('partie/full_code_partie.html.twig');
                }

           } else {
                return $this->render('partie/started_code_partie.html.twig');
            }
          }
          else {
            return $this->redirectToRoute('affiche_code_partie', ['partie' => $partie]);
         }

    }

    /**
     * @Route("/saisir-code", name="saisir_code")
     */

    public function saisirCode(Request $request){

        if ($request->isMethod('POST')) {

            $code = $request->request->get('code');

            if (ctype_digit($code)) {
                return $this->redirectToRoute('rejoindre_partie', ['partie' => $code]);
            } else {
                return $this->redirectToRoute('saisir_code');
            }

        } else {
            return $this->render('partie/saisirCodePartie.html.twig');
        }

    }

    /**
     * @Route("/commencer-partie/{partie}", name="commencer_partie")
     * @param Partie $partie
     *
     * @return Response
     */
    public function commencerPartie(Request $request, PartieRepository $partieRepository, Partie $partie, JouerRepository $jouerRepository)
    {

        $jouers = $jouerRepository->findBy(['partie' => $partie]);
        if (count($jouers) >1)
        {
            $em = $this->getDoctrine()->getManager();
            $partie->setEtatPartie("C");
            $partie->setQuiJoue($jouers[0]->getId());
            $em->persist($partie);
            $em->flush();//sauvegarde de l'entité partie

        return $this->redirectToRoute('affiche_partie', ['partie' => $partie->getId()]);
        } else {
            return $this->render('partie/pasassezdejoueurs.html.twig',
                [
                    'partie' => $partie
                ]);
        }
    }

    /**
     * @Route("/affiche-tchat-partie/{partie}", name="affiche_tchat_partie")
     * @param partie $partie
     *
     * @return Response
     */
    public function afficheTchatPartie(Request $request, TchatsRepository $tchatsRepository, Partie $partie)
    {
        $texte = $tchatsRepository->findBy(['partie' => $partie]);
        $tchats = array_reverse($texte);
        $user = $this->getUser();

        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();
            $nouveautchat = new Tchats();
            $nouveautchat->setMessage($request->request->get('message'));
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($user);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);
            $em->flush();


        }


        return $this->render('partie/afficheTchatPartie.html.twig',
            [
                'partie' => $partie,
                'tchats' =>$tchats
            ]);
    }

    /**
     * @Route("/affiche-tableau-partie/{partie}", name="affiche_tableau_partie")
     * @param partie $partie
     *
     * @return Response
     */
    public function afficheTableauPartie(Partie $partie)
    {

        return $this->render('partie/affiche_tableau_partie.html.twig',
            [
                'partie' => $partie
            ]);
    }

    /**
     * @Route("/affiche-argent-joueur/{partie}", name="affiche_argent_joueur")
     * @param partie $partie
     *
     * @return Response
     */
    public function afficheargentjoueur(Partie $partie, JouerRepository $jouerRepository)
    {
        $user = $this->getUser();
        $jouer = $jouerRepository->findBy(['joueur' => $user, 'partie' => $partie])[0];
        return $this->render('partie/affiche_argent_joueur.html.twig',
            [
                'partie' => $partie,
                'jouer' => $jouer
            ]);
    }

    /**
     * @Route("/affiche-partie/{partie}", name="affiche_partie")
     * @param Partie $partie
     *
     * @return Response
     */
    public function affichePartie(Partie $partie, Request $request, TchatsRepository $tchatsRepository, JouerRepository $jouerRepository)
    {

        if ($partie->getEtatPartie() == 'F'){
            return $this->redirectToRoute('fin_code_partie', ['partie' => $partie->getId()]);
        }
        $tchats = $tchatsRepository->findBy(['partie' => $partie] );
        $user = $this->getUser();
        $jouer = $jouerRepository->findBy(['joueur' => $user, 'partie' => $partie])[0];
        $argent = $jouer->getArgent();
        $facture = $jouer->getFacture();

        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();
            $nouveautchat = new Tchats();
            $nouveautchat->setMessage($request->request->get('message'));
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($user);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);
            $em->flush();


        }
        return $this->render('partie/affichePartie.html.twig',
            [
                'facture' => $facture,
                'partie' => $partie,
                'argent' => $argent
            ]);
    }

    /**
     * @Route("/update-partie/data/{partie}", name="update_game")
     * @param Partie $partie
     *
     * @return Response
     */
    public function updateGame(Partie $partie, JouerRepository $jouerRepository)
    {
        $jouers = $partie->getJouers();
        $joueur = $jouerRepository->findBy([ 'joueur' => $this->getUser(), 'partie' => $partie])[0];
        $monTour = false;
        $positions = [];
        if ($partie->getQuiJoue() === $joueur->getId())
        {
            //quiJoue contient l'id du joueur en train de jouer.
            $monTour = true;
        }
        foreach ($jouers as $jouer) {
            if ($jouer->getJoueur() !== null) {
                $positions[$jouer->getJoueur()->getId()]['username'] = $jouer->getJoueur()->getUsername();
                $positions[$jouer->getJoueur()->getId()]['position'] = $jouer->getPosition();
                $positions[$jouer->getJoueur()->getId()]['argent'] = $jouer->getArgent();
            }
        }

        $array = [
            'joueurEnCours' => $partie->getQuiJoue(),
            'monTour' => $monTour,
            'positionsJoueurs' => $positions,
            'partie' => $partie
        ];

        return $this->json($array);
    }
    /**
     * @Route("/update-partie/fin-tour/{partie}", name="fin_de_tour")
     * @param Partie $partie
     *
     * @return Response
     */
    public function finTour(EntityManagerInterface $entityManager, Partie $partie, JouerRepository $jouerRepository)
    {
        $partie->getQuiJoue();
        $jouers = $partie->getJouers();
        $nbjouers = count($jouers);

        $positions = [];
        foreach ($jouers as $jouer) {
            if ($jouer->getJoueur()->getId() === $this->getUser()->getId())
            {
                $monOrdre = $jouer->getClassement();
            }
        }

        if ($monOrdre == $nbjouers){
            $classementsuivant = 1;
        } else {
            $classementsuivant = $monOrdre+1;
        }

        $joueurobjetSuivant = $jouerRepository->findBy(['partie' => $partie, 'classement' => $classementsuivant])[0];
        $joueurSuivant = $joueurobjetSuivant->getId();
        $partie->setQuiJoue($joueurSuivant);
        $entityManager->persist($partie);
        $entityManager->flush();//sauvegarde de l'entité partie
        $array = [
            'joueurEnCours' => $partie->getQuiJoue(),
            'monTour' => false,
            'positionsJoueurs' => $positions
        ];

        return $this->json($array);
    }




    /**
     * @Route("/update-partie/lancé dé/{partie}", name="lance_de")
     * @param EntityManagerInterface $entityManager
     * @param JouerRepository        $jouerRepository
     * @param Partie                 $partie
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function lanceDe(EntityManagerInterface $entityManager,UserRepository $userRepository, JouerRepository $jouerRepository,  Partie $partie, CasesRepository $casesRepository, CarteRepository $carteRepository, TchatsRepository $tchatsRepository)
    {
        $jouer = $jouerRepository->findBy(['joueur' => $this->getUser(), 'partie' => $partie] )[0];
        $nom = $jouer->getJoueur()->getName();
        $de = rand(1,6);
        $position = $jouer->getPosition()+$de;
        $message = '';
        $finTour=false;

        if ($position > 30) {
            $position = $position-31; //fin du tour
            $tour = $jouer->getTour();
            $tour++;
            $jouer->setTour($tour);
            $fact = $jouer->getFacture();
            $comp = $jouer->getCompetence();
            $sous = $jouer->getArgent();
            $gain = $comp*300;
            $sous = $sous+$gain-$fact;
            $jouer->setArgent($sous);
            $jouer->setCompetence(0);
            $fact = $jouer->getFacture();


            $message = $message.$nom.' passe par la case départ et empoche '.$gain.'€ avec ses compétences. ';

            if ($tour > 4){
                $partie->setEtatPartie('F');
                $jouers = $jouerRepository->findBy(['partie' => $partie]);
                $gagnant = $jouers[0];
                foreach ($jouers as $jouer){

                    //ce truc met les scores
                    $joueur = $jouer->getJoueur();
                    $score = $joueur->getScore();
                    $argent = $jouer->getArgent();
                    $joueur->setScore($score+$argent);
                    $entityManager->persist($joueur);
                    $entityManager->flush();// sauvegarde des scores

                    if ($argent > $gagnant->getArgent()){
                        $gagnant = $jouer;
                    }

                }
                $partie->setGagnant($gagnant->getId());
                $gagne = $gagnant->getJoueur();
                $gagnenom = $gagne->getName();
                $partie->setDateFin(new \DateTime('now'));
                $entityManager->persist($partie);
                $entityManager->flush();//sauvegarde de l'entité partie
                $message = $message.' '.$gagnenom.' Remporte la partie.';

                $narrateur = $userRepository->findBy(['id' => 1])[0];
                $em = $this->getDoctrine()->getManager();
                $nouveautchat = new Tchats();
                $nouveautchat->setMessage($message);
                $nouveautchat->setPartie($partie);
                $nouveautchat->setUser($narrateur);
                $nouveautchat->setTemps(new \DateTime('now'));
                $em->persist($nouveautchat);
                $em->flush();


                return $this->redirectToRoute('fin_code_partie', ['partie' => $partie->getId()]);
            }




        }


        $case = $casesRepository->findBy(['position' => $position])[0];


        switch ($case->getEffet()) {

            //le champs effet doit permettre de savoir quoi faire sur la case
            //j'ai ajouté un champs "complement" pour avoir des infos sur la valeur de l'action de la case: exemple 2 cartes courriers

            case 'Histoire Vraie':

                $chiffre = rand(1,25);
                $cartechoisie = $carteRepository->findBy(['typeDeCarte' => 'H'])[$chiffre];
                $animation2 = 'histoire.gif';

                switch ($cartechoisie->getEffet()){

                    case 'Recule':
                        $long = $cartechoisie->getCout();
                        $position = $position-$long;
                        $message = $message.'Carte Histoire Vraie : '.$nom.' recule de '.$long.' cases';

                        break;

                    case 'Avance':
                        $long = $cartechoisie->getCout();
                        $position = $position+$long;
                        $message = $message.'Carte Histoire Vraie : '.$nom.' avance de '.$long.' cases';

                        break;

                    case 'gainC':
                        $nb = $cartechoisie->getCout();
                        $comp = $jouer->getCompetence()+$nb;
                        $jouer->setCompetence($comp);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' gagne '.$nb.' point de compétence';

                        break;

                    case 'payeC':
                        $nb = $cartechoisie->getCout();
                        $comp = $jouer->getCompetence()-$nb;
                        $jouer->setCompetence($comp);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' perd '.$nb.' point de compétence';

                        break;

                    case 'gainA':
                        $khalass = $cartechoisie->getCout();
                        $sous = $jouer->getArgent()+$khalass;
                        $jouer->setArgent($sous);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' gagne '.$khalass.'€';

                        break;

                    case 'payeA':
                        $khalass = $cartechoisie->getCout();
                        $sous = $jouer->getArgent()-$khalass;
                        $jouer->setArgent($sous);

                        $cagn = $partie->getCagnotte();
                        $cagn = $cagn+$khalass;
                        $partie->setCagnotte($cagn);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' perd '.$khalass.'€';

                        break;

                    case 'Jackpot':
                        $cagn = $partie->getCagnotte();
                        $sous = $jouer->getArgent()+$cagn;
                        $jouer->setArgent($sous);
                        $partie->setCagnotte(0);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' empoche '.$cagn.'€ venant de la cagnotte';

                        break;

                    case 'Vente':
                        $comp = $jouer->getCompetence();
                        $sous = $jouer->getArgent();
                        $gain = $comp*300;
                        $sous = $sous+$gain;
                        $jouer->setArgent($sous);
                        $jouer->setCompetence(0);
                        $message = $message.'Carte Histoire Vraie : '.$nom.' empoche '.$gain.'€ avec ses compétences';

                        break;


                }
                break;

                        case 'Notifications':

                            $chiffrenotif = rand(1,25);
                            $cartechoisienotif = $carteRepository->findBy(['typeDeCarte' => 'N'])[$chiffrenotif];
                            $animation2 = 'notification.gif';

                            switch ($cartechoisienotif->getEffet()){

                                case 'gainA':
                                    $khalass = $cartechoisienotif->getCout();
                                    $sous = $jouer->getArgent()+$khalass;
                                    $jouer->setArgent($sous);
                                    $message = $message.'Carte Notification : '.$nom.' gagne '.$khalass.'€';

                                    break;

                                case 'payeA':
                                    $khalass = $cartechoisienotif->getCout();
                                    $sous = $jouer->getFacture()+$khalass;
                                    $jouer->setFacture($sous);

                                    $cagn = $partie->getCagnotte();
                                    $cagn = $cagn+$khalass;
                                    $partie->setCagnotte($cagn);
                                    $message = $message.'Carte Notification : '.$nom.' prend '.$khalass.'€ de facture';

                                    break;

                                case 'Rien':

                                    $message = $message.$nom.' se fait encore spam sa boîte URCA par l\'administration...';

                                    break;

                            }


                            break;


                        case 'CM':

                            $comp = $jouer->getCompetence()+2;
                            $jouer->setCompetence($comp);
                            $message = $message.$nom.' gagne 2 points de compétence en CM';

                            break;

                        case 'Partiel':

                            $comp = $jouer->getCompetence()-1;
                            $jouer->setCompetence($comp);
                            $message = $message.$nom.' perd 1 point de compétence, il fallait mieux réviser pour les partiels !';

                            break;

            case 'Anglais':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours d\'anglais';

                break;

            case 'Communication':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours de com\'';

                break;

            case 'Développement':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours de développement';

                break;

            case 'Audiovisuel':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours d\'audiovisuel';

                break;

            case 'Intégration':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours d\'intégration';

                break;

            case 'Infographie':

                $comp = $jouer->getCompetence()+1;
                $jouer->setCompetence($comp);
                $message = $message.$nom.' gagne 1 point de compétence en cours d\'infographie';

                break;

                        case 'Jackpot':

                            $cagn = $partie->getCagnotte();
                            $sous = $jouer->getArgent()+$cagn;
                            $jouer->setArgent($sous);
                            $partie->setCagnotte(0);
                            $message = $message.'Jackpot pour '.$nom.' qui empoche '.$cagn.'€ venant de la cagnotte';

                            break;

                     //   case 'Transaction':
                            //on pioche une carte transaction qu'on propose au joueur
                            //il faut piocher des cartes courriers et les mettre dans la main du joueur
                            //         $tabCartes = $partie->getPioche(); //je récupère les cartes courriers de la pioche
                            //         $mesCartes = $jouer->getCartes(); //je récupère mes cartes
                            //todo: il faudrait tester si j'ai assez de carte...
                            //on dépile la carte transaction de la pioche, et on empile dans joueur.
                            //           $carte = array_pop($tabCartes['T']);
                            //           $mesCartes['T'][] = $carte;
                            //           $data = $carte; //je sauvegarde aussi dans un tableau intermédiaire pour afficher en JS plus facilement
                            //mise à jour de partie pour la pioche, et de jouer pour mes cartes
                            //        $partie->setPioche($tabCartes);
                            //         $jouer->setCartes($mesCartes);
                        //         break;

                        case 'Repos':
                            $message = $message.$nom.' se repose et passe son tour';
                            break;



                            //  case 'Vente':
                            //Vente possible des transactions
                            //récupère toutes les cartes transaction du jour pour qu'il puisse choisir celle(s) à vendre
                       //       $data = $jouer->getCartes()['T'];
                       //       break;

        }



        if (!empty($message)){
            $narrateur = $userRepository->findBy(['id' => 1])[0];
            $em = $this->getDoctrine()->getManager();
            $nouveautchat = new Tchats();
            $nouveautchat->setMessage($message);
            $nouveautchat->setPartie($partie);
            $nouveautchat->setUser($narrateur);
            $nouveautchat->setTemps(new \DateTime('now'));
            $em->persist($nouveautchat);
            $em->flush();
        }
        $jouer->setPosition($position);
        $entityManager->persist($jouer);
        $entityManager->flush();//sauvegarde de l'entité partie
        $animationde = 'lancede'.$de.'.gif';
        $array = [
            'de' => $de,
            'finTour' => $finTour,
            'position' => $position,
            'animation' => $animationde
        ];

        return $this->json($array);

        //fonction fin de tour redirection
    }

    /**
     * @Route("/pioche-acqui/{partie}", name="pioche_acqui")
     * @param Partie $partie
     *
     * @return Response
     */

    public function piocheAcqui(AcquisitionsRepository $acquisitionsRepository, PartieRepository $partieRepository, Partie $partie, AcquerirRepository $acquerirRepository){





}


    /**
     * @Route("/affiche-plateau/{partie}", name="affiche_plateau")
     * @param Partie $partie
     *
     * @return Response
     */
    public function affichePlateau(JouerRepository $jouerRepository, CasesRepository $casesRepository, Partie $partie)
    {

        if ($partie->getEtatPartie()== 'F'){
            return $this->render('partie/_affichePlateauFin.html.twig',
                [
                    'partie' => $partie

                ]);
        }

        $jouers = $partie->getJouers();

        $positions = [];

        $joueuractuel = $jouerRepository->findBy(['joueur' => $this->getUser(), 'partie' => $partie]);
        $positionuser = $joueuractuel[0]->getPosition();

        $cases = $casesRepository->getSixCases($positionuser);

        foreach ($jouers as $jouer) {
            if (!array_key_exists($jouer->getPosition(), $positions)) {
                $positions[$jouer->getPosition()] = [];
            }
            $positions[$jouer->getPosition()][] = $jouer;
        }

        return $this->render('partie/_affichePlateau.html.twig',
            [
                'cases' => $cases,
                'partie' => $partie,
                'positions' => $positions
            ]);
    }

}


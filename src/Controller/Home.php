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
use DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class Home extends AbstractController {

    /**
     * @Route("/home", name="app_home")
     */

    public function home(JouerRepository $jouerRepository, PartieRepository $partieRepository, UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder){


        if ($request->isMethod('POST')) {

            $user = $this->getUser();


            $newpseudo = $request->request->get('pseudo');
            $em = $this->getDoctrine()->getManager();
            $user->setName($newpseudo);
            $em->persist($user);
            $em->flush();//sauvegarde de l'entité partie

        }

        $user = $this->getUser();
        $id = $user->getId();

        $jouers = $jouerRepository->findBy(['joueur' => $user]);
        $fini = $partieRepository->findBy(['etatPartie' => 'F']);

        $wincount = 0;
        foreach ($fini as $partiefinie){
            $partjouer = $jouerRepository->findBy(['id' => $partiefinie->getGagnant()])[0];
            if ($partjouer->getJoueur() == $user){
                $wincount++;
            }
        }


        $allusers = $userRepository->getAllUserOrderedByScore();

        $classement = array();
        $classement[] = $allusers[0];
        $classement[] = $allusers[1];
        $classement[] = $allusers[2];
        $classement[] = $allusers[3];
        $classement[] = $allusers[4];
        $classement[] = $allusers[5];
        $classement[] = $allusers[6];

        $scoretest = $allusers[6]->getScore();

        if($scoretest >= $user->getScore()){
            $fond = true;

        } else {
            $fond = false;
        }

        $date = new DateTime('now');
        return $this->render('home.html.twig',
            [
                'fond' => $fond,
                'classement' => $classement,
                'date' => $date->format('d/m/Y H:i'),
                'jouers' => $jouers,
                'win' => $wincount
            ]);
    }

    //  /**
    //   * @Route("/admin/users", name="app_all_users")
    //   */
    // public function afficheAllUsers()
    // {
    //   $users = $this->getDoctrine()->getRepository(User::class)->findAll();

    //  return $this->render('security/affiche_users.html.twig',
    //    ['users' => $users]);
    // }

}
<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 27/08/2018
 * Time: 17:12
 */

namespace App\Controller;


use App\Entity\Media;
use App\Form\MediaType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * Ajouter un média
     *
     * @Route("/add/media/", name="addMedia")
     */
    public function addMedia(Request $request, ObjectManager $manager) {

        $media = new Media();

        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            //$file = $media->getUrl();
            $file = $form->get('url')->getData();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('images'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $media->setUrl($fileName);

            $media->setType('i');
            $manager->persist($media);
            $manager->flush();

            $this->addFlash('success', 'Le trick a bien été ajouté!');
            return $this->redirectToRoute('homepage');
        }
        return $this->render(
            'Media/addMedia.html.twig', [
            'formAddMedia' => $form->createView()
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
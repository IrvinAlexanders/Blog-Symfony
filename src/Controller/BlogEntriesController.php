<?php

namespace App\Controller;

use App\Entity\BlogEntries;
use App\Form\BlogEntriesType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class BlogEntriesController extends AbstractController
{
    /**
     * @Route("/nueva-entrada", name="new_entries")
     */
    public function agregarEntrada(Request $request, SluggerInterface $slugger): Response
    {
        $Entrada = new BlogEntries();
        $form = $this->createForm(BlogEntriesType::class, $Entrada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->subirImagen($form, $Entrada, $slugger);

            $link = $this->getDoctrine()->getManager();

            $Entrada->setUser($this->getUser());

            $link->persist($Entrada);
            $link->flush();
            // $this->addFlash('correcto', BlogEntriesType::REGISTRO_EXITOSO);
            return $this->redirectToRoute('blog',['list' => 'mis-entradas']);
        }

        return $this->render('blog_entries/agregar_entrada.html.twig', [
            'formulario' => $form->createView()
        ]);
    }

    /**
     * @Route("/editar-entrada/{entrada_id}", name="edit_entry")
     */
    public function editarEntrada($entrada_id, Request $request, SluggerInterface $slugger): Response
    {
        $link = $this->getDoctrine()->getManager();
        $Entrada = $link->getRepository(BlogEntries::class)->find($entrada_id);

        if (!$Entrada) {
            // throw $this->createNotFoundException("Entrada no encontrada");
            $this->addFlash('error', 'Entrada no encontrada.');
            return $this->redirectToRoute('blog',[
                'entrada_id' => $entrada_id
            ]);
        }elseif ($Entrada->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'Usted no puede editar esta entrada.');
            return $this->redirectToRoute('view_entry',[
                'entrada_id' => $Entrada->getId()
            ]);
        }

        $form = $this->createForm(BlogEntriesType::class, $Entrada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->subirImagen($form, $Entrada, $slugger);

            $link->flush();

            return $this->redirectToRoute('view_entry',[
                'entrada_id' => $Entrada->getId()
            ]);
        }

        return $this->render('blog_entries/editar_entrada.html.twig', [
            'formulario' => $form->createView()
            , 'entrada' => $Entrada
        ]);
    }

    /**
     * @Route("/ver-entrada/{entrada_id}", name="view_entry")
     */
    public function verEntrada($entrada_id): Response
    {
        $link = $this->getDoctrine()->getManager();
        $Entrada = $link->getRepository(BlogEntries::class)->find($entrada_id);

        if (!$Entrada) {
            // throw $this->createNotFoundException("Entrada no encontrada");
            $this->addFlash('error', 'Entrada no encontrada.');
            return $this->redirectToRoute('blog',[
                'entrada_id' => $entrada_id
            ]);
        }

        return $this->render('blog_entries/ver_entrada.html.twig', [
            'entrada' => $Entrada
        ]);
    }

    /**
     * @Route("/blog/{list}", name="blog")
     */
    public function verBlog($list='todo', PaginatorInterface $paginator, Request $request): Response
    {
        $link = $this->getDoctrine()->getManager();
        
        $Entradas = $link->getRepository(BlogEntries::class)->buscarEntradasBlog($list == 'mis-entradas' ? $this->getUser()->getId() : false);
        $pagination = $paginator->paginate(
            $Entradas, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('blog_entries/blog.html.twig', [
            'posts' => $Entradas,
            'pagination' => $pagination,
            'list' => $list
        ]);
    }
    
    private function subirImagen($form, BlogEntries &$Entrada, SluggerInterface $slugger)
    {
        $brochureFile = $form->get('image')->getData();
        if ($brochureFile) {
            $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $brochureFile->move(
                    $this->getParameter('images_blogs_entrities_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception("Error en la subida", 1);
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $Entrada->setImage($newFilename);
        }
    }
}